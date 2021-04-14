<?php
/** TODO
 * 1. endpoints de transações
 */
namespace API;
require __DIR__ . '/../vendor/autoload.php';

use API\Exceptions\TransactionAPIException;
use API\Exceptions\UserAPINonexistentId;
use midorikocak\nano\Api;
use API\Exceptions\UserAPIException;

$api = new Api();
$user = new User(__DIR__."/../users.json");

define("HTTP_STATUS_CODE_OK", 200);
define("HTTP_STATUS_CODE_CREATED", 201);
define("HTTP_STATUS_CODE_NOT_FOUND", 404);
define("HTTP_STATUS_CODE_BAD_REQUEST", 400);

$usersEndPoint = '/users';
$consumersEndPoint = '/consumers';
$sellersEndPoint = '/sellers';
$transactionsEndPoint = '/transactions';

$api->get('/', function () {
    echo json_encode(['message' => "Hello, World!"]);
    http_response_code(HTTP_STATUS_CODE_OK);
});

$api->get($usersEndPoint, function () use ($user) {
    echo json_encode(array_values($user->getUsers()));
});

$api->get($consumersEndPoint, function () use ($user) {
    echo json_encode(array_values($user->getConsumers()));
});

$api->get($sellersEndPoint, function () use ($user) {
    echo json_encode(array_values($user->getSellers()));
});

$api->post($usersEndPoint, function () use ($user) {
    $input = json_decode(file_get_contents('php://input'), true);
    try {
        $user->registerUser($input);
    } catch (UserAPIException $e) {
        echo json_encode(['error' => $e->error, "description" => $e->getMessage()]);
        return http_response_code(HTTP_STATUS_CODE_BAD_REQUEST);
    }

    echo json_encode(["users" => $user->totalOfUsers(), "consumers" => $user->totalOfConsumers(), "sellers" => $user->totalOfSellers()]);
    http_response_code(HTTP_STATUS_CODE_CREATED);
});


$api->get($usersEndPoint.'/{id}', function ($id) use ($user) {
    try {
        $user = $user->getUserById($id);
    } catch (UserAPINonexistentId $e) {
        echo json_encode(['error' => "id_not_found"]);
        return http_response_code(HTTP_STATUS_CODE_NOT_FOUND);
    }
    echo json_encode($user);
    return http_response_code(HTTP_STATUS_CODE_OK);
});

$api->delete($usersEndPoint, function () use ($user) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'];
    try {
        $user->deleteUser($id);
    } catch (UserAPINonexistentId $e) {
        echo json_encode(['error' => "id_not_found"]);
        return http_response_code(HTTP_STATUS_CODE_NOT_FOUND);
    }
    echo json_encode(["users" => $user->totalOfUsers(), "consumers" => $user->totalOfConsumers(), "sellers" => $user->totalOfSellers()]);
    http_response_code(HTTP_STATUS_CODE_OK);
});

$api->post($transactionsEndPoint, function () use ($user) {
    $input = json_decode(file_get_contents('php://input'), true);
    $transaction = $user->createTransaction($input["value"], $input["payerId"], $input["receiverId"]);
    try {
        $transaction->execute();
    } catch (TransactionAPIException $e) {
        echo json_encode(['error' => $e->error, "description" => $e->getMessage()]);
        return http_response_code(HTTP_STATUS_CODE_BAD_REQUEST);
    }

    $payer = $user->getUserById($input["payerId"]);
    $receiver = $user->getUserById($input["receiverId"]);
    echo json_encode(["payerBalance" => $payer["balance"], "receiverBalance" => $receiver["balance"]]);
    http_response_code(HTTP_STATUS_CODE_OK);
});