<?php

require 'app/services/UserService.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch ($uri) {
	case '/':
		require 'app/controllers/IndexController.php';
		break;

	case '/user':
		require 'app/controllers/FormController.php';
		break;

	case '/api/v1/users':
		if ($method === 'GET') {
			UserService::getUser();
		}
		break;

	case '/api/v1/users/create':
		if ($method === 'POST') {
			UserService::createUser();
		}
		break;

	case '/api/v1/users/update':
		if ($method === 'PATCH') {
			UserService::updateUser();
		}
		break;

	case '/api/v1/users/delete':
		if ($method === 'DELETE') {
			UserService::deleteUser();
		}
		break;

	default:
		header("HTTP/1.0 404 Not Found");
		echo json_encode(["success" => false, "message" => "Route not found"]);
		break;
}