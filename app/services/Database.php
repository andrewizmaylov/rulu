<?php

class Database {
	protected array $config = [
		'host' => '185.177.216.77',
		'port' => 3306,
		'dbname' => 'lZcBczlu',
		'charset' => 'utf8mb4',
	];
	protected array $credentials = [
		'user' => 'ocyYKj',
		'pass' => 'AJOjmeqNVnvNSijU',
	];
	protected array $options = [
		\PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,       // Fetch results as associative arrays
		\PDO::ATTR_EMULATE_PREPARES   => false,                  // Use native prepared statements
	];
	public \PDO $pdo;

	public function __construct() {
		$dsn = 'mysql:' . http_build_query($this->config, '', ';');
		try {
			$this->pdo = new \PDO($dsn, $this->credentials['user'], $this->credentials['pass'], $this->options);
		} catch (\PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}

	public function query(string $query)
	{
		$statement = $this->pdo->prepare($query);
		$statement->execute();

		return $statement;
	}
}