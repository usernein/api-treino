<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/users.php';

use midorikocak\nano\Api;

$api = new Api();
$users = new User();

define("HTTP_STATUS_CODE_OK", 200);
define("HTTP_STATUS_CODE_CREATED", 201);
define("HTTP_STATUS_CODE_NOT_FOUND", 404);
define("HTTP_STATUS_CODE_BAD_REQUEST", 400);

$usersEndPoint = '/users';
$consumersEndPoint = '/consumers';
$sellersEndPoint = '/sellers';

$api->get('/', function () {
    echo json_encode(['message' => "Hello, World!"]);
    http_response_code(HTTP_STATUS_CODE_OK);
});

$api->get($usersEndPoint, function () use ($users) {
    echo json_encode($users->getUsers(), JSON_PRETTY_PRINT);
});

$api->get($consumersEndPoint, function () use ($users) {
    echo json_encode($users->getConsumers(), JSON_PRETTY_PRINT);
});

$api->get($sellersEndPoint, function () use ($users) {
    echo json_encode($users->getSellers(), JSON_PRETTY_PRINT);
});

$api->post($usersEndPoint, function () use ($users) {
    $input = json_decode(file_get_contents('php://input'), true);
    try {
        $result = $users->registerUser($input);
    } catch (UserAPIException $e) {
        echo json_encode(['error' => $e->error, "description" => $e->getMessage()]);
        return http_response_code(HTTP_STATUS_CODE_BAD_REQUEST);
    }

    echo json_encode(["users" => $users->totalOfUsers(), "consumers" => $users->totalOfConsumers(), "sellers" => $users->totalOfSellers()]);
    http_response_code(HTTP_STATUS_CODE_CREATED);
});


$api->get($sellersEndPoint.'/{id}', function ($id) use ($users) {
    $user = $users->getUserById($id);
    if (!$user) {
        echo json_encode(['error' => "id_not_found"]);
        return http_response_code(HTTP_STATUS_CODE_NOT_FOUND);
    }
    echo json_encode($user, JSON_PRETTY_PRINT);
    return http_response_code(HTTP_STATUS_CODE_OK);
});

$api->delete($sellersEndPoint, function () use ($users) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    try {
        $users->deleteUser($id);
    } catch (UserAPIException $e) {
        echo json_encode(["error" => $e->error, "description" => $e->getMessage()]);
        return http_response_code(HTTP_STATUS_CODE_BAD_REQUEST);
    }
    echo json_encode(["users" => $users->totalOfUsers(), "consumers" => $users->totalOfConsumers(), "sellers" => $users->totalOfSellers()]);
    http_response_code(HTTP_STATUS_CODE_OK);
});