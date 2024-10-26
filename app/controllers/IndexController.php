<?php

$title = 'Index Page';

$response = json_decode(UserService::getUser(), true);
$data = $response['result'];

include "app/views/index.page.php";