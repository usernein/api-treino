<?php
require __DIR__.'/vendor/autoload.php';

use midorikocak\nano\Api;

$api = new Api();
$users = file_exists('users.json') ? file_get_contents('users.json') : '[]';
$users = json_decode($users, true);

$api->get('/', function () {
    echo json_encode(['message' => "Hello, World!"]);
    http_response_code(200);
});

$api->get('/abc/{message}', function ($message) {
    echo json_encode(['message' => "Hello, {$message}!"]);
    http_response_code(200);
});

$api->post('/user', function () use (&$users) {
    $input = (array)json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
    $users[$input['id']] = $input;
    file_put_contents('users.json', json_encode($users));
    echo json_encode(["total" => count($users)]);
    http_response_code(201);
});

$api->get('/user', function () use ($users) {
    echo json_encode(array_values($users), 480);
    http_response_code(200);
});

$api->get('/user/{id}', function ($id) use ($users) {
    if (isset($users[$id])) {
        echo json_encode($users[$id], 480);
        return http_response_code(200);
    }
    http_response_code(404);
});