<?php

require 'Database.php';
class UserService
{
	private static PDO $pdo;

	private static function init()
	{
		self::$pdo = (new Database())->pdo;
	}

	private static function sanitize(): array {
		$content = file_get_contents('php://input');
		if (self::isApiRequest()) {
			$data = json_decode($content, true);
		} else {
			parse_str($content, $data);
		}

		return [
			'full_name' => htmlspecialchars(trim($data['full_name'])) ?? null,
			'role' => htmlspecialchars(trim($data['role'])) ?? null,
			'efficiency' => htmlspecialchars(trim($data['efficiency'])) ?? null,
		];
	}

	private static function respond($data): false|string|null
	{
		$jsonData = json_encode($data);

		if (self::isApiRequest()) {
			echo $jsonData;
			return null;  // Explicitly return null when echoing
		} else {
			return $jsonData;
		}
	}

	public static function createUser(): false|string|null
	{
		self::init();
		try {
			// Получение данных из POST-запроса
			$data = self::sanitize();

			if (empty($data['full_name'])) {
				throw new \Exception('Invalid payload: full_name is required.');
			}

			// Подготовка и выполнение SQL-запроса
			$stmt = static::$pdo->prepare("INSERT INTO `users` (full_name, role, efficiency) VALUES (:full_name, :role, :efficiency)");
			$stmt->execute($data);

			// Получаем ID нового пользователя
			$userId = static::$pdo->lastInsertId();

			return self::respond([
				"success" => true,
				"result"  => ["id" => $userId]
			]);
		} catch (\Exception $e) {
			return self::respond([
				"success" => false,
				"result"  => ["error" => $e->getMessage()]
			]);
		}
	}

	public static function getUser(): false|string|null
	{
		self::init();
		if (!isset($_GET['id']) && parse_url($_SERVER['REQUEST_URI'])['path'] === '/user') {
			return self::respond([
				"success" => true,
				"result"  => null
			]);
		}
		try {
			$userId = $_GET['id'] ?? null;
			$queryParams = [];

			// Базовый запрос
			$query = "SELECT * FROM `users` WHERE 1=1";

			// Добавляем ID в запрос, если передан
			if ($userId) {
				$query .= " AND id = :id";
				$queryParams['id'] = $userId;
			}

			// Добавляем фильтры, если переданы
			if (isset($_GET['full_name'])) {
				$query .= " AND full_name = :full_name";
				$queryParams['full_name'] = $_GET['full_name'];
			}
			if (isset($_GET['role'])) {
				$query .= " AND role = :role";
				$queryParams['role'] = $_GET['role'];
			}
			if (isset($_GET['efficiency'])) {
				$query .= " AND efficiency = :efficiency";
				$queryParams['efficiency'] = $_GET['efficiency'];
			}

			$stmt = static::$pdo->prepare($query);
			$stmt->execute($queryParams);
			$user = $userId ? $stmt->fetch() : $stmt->fetchAll();

			if ($user) {
				return self::respond([
					"success" => true,
					"result"  => $user
				]);
			} else {
				throw new Exception("User not found");
			}
		} catch (Exception $e) {
			return self::respond([
				"success" => false,
				"result"  => ["error" => $e->getMessage()]
			]);
		}
	}

	public static function updateUser(): false|string|null
	{
		$userId = $_GET['id'] ?? null;
		if (!$userId) {
			return self::respond([
				"success" => false,
				"result" => ["error" => 'No user id provided']
			]);
		}
		self::init();
		try {
			$data = self::sanitize();
			$fieldsToUpdate = [];
			$params = [];

			foreach (['full_name', 'role', 'efficiency'] as $field) {
				if (isset($data[$field])) {
					$fieldsToUpdate[] = "$field = :$field";
					$params[$field] = $data[$field];
				}
			}

			if (empty($fieldsToUpdate)) {
				throw new Exception("No fields to update provided");
			}

			$params['id'] = $userId;
			$sql = "UPDATE `users` SET " . implode(", ", $fieldsToUpdate) . " WHERE id = :id";
			$stmt = static::$pdo->prepare($sql);
			$stmt->execute($params);

			$updatedUser = static::$pdo->prepare("SELECT * FROM `users` WHERE id = :id");
			$updatedUser->execute(['id' => $userId]);
			$user = $updatedUser->fetch();

			return self::respond([
				"success" => true,
				"result" => $user
			]);
		} catch (Exception $e) {
			return self::respond([
				"success" => false,
				"result" => ["error" => $e->getMessage()]
			]);
		}
	}

	public static function deleteUser(): false|string|null
	{
		$userId = $_GET['id'] ?? null;
		self::init();
		try {
			if ($userId) {
				// Удаление пользователя по ID
				$stmt = static::$pdo->prepare("SELECT * FROM `users` WHERE id = :id");
				$stmt->execute(['id' => $userId]);
				$user = $stmt->fetch();

				if (!$user) {
					throw new Exception("User not found");
				}

				$deleteStmt = static::$pdo->prepare("DELETE FROM `users` WHERE id = :id");
				$deleteStmt->execute(['id' => $userId]);

				return self::respond([
					"success" => true,
					"result" => $user
				]);
			} else {
				// Удаление всех пользователей
				static::$pdo->exec("DELETE FROM `users`");
				return self::respond(["success" => true]);
			}
		} catch (Exception $e) {
			return self::respond([
				"success" => false,
				"result" => ["error" => $e->getMessage()]
			]);
		}
	}

	/**
	 * @return bool
	 */
	public static function isApiRequest(): bool
	{
		return strrpos(parse_url($_SERVER['REQUEST_URI'])['path'], '/api/') === 0;
	}
}