<?php
/** TODO
 * 1. Teste unitário
 */
namespace API;

define("USER_TYPE_CONSUMER", "consumer");
define("USER_TYPE_SELLER", "seller");

use API\Exceptions\{UserAPINotEnoughData, UserAPIDuplicatedData, UserAPIInvalidData};
class User
{
    private Storage $storage;

    public function __construct()
    {
        $this->storage = new Storage('users.json');
    }

    public function registerUser(array $userFields) : bool
    {
        if (!isset($userFields['name']) || !isset($userFields['document']) || !isset($userFields['type'])) {
            throw new UserAPINotEnoughData();
        }

        $userData = [
            'id' => uniqid(),
            'name' => $userFields['name'],
            'balance' => 0,
            'document' => (int)$userFields['document'],
            'type' => $userFields['type'],
        ];
        if (!$userData['name'] || !is_string($userData["name"])) {
            throw new UserAPIInvalidData("name");
        }
        if (!$userData['type'] || !is_string($userData["type"]) || !in_array($userData['type'], [USER_TYPE_CONSUMER, USER_TYPE_SELLER])) {
            throw new UserAPIInvalidData("type");
        }
        if (!$userData['document']) {
            throw new UserAPIInvalidData("document");
        }

        $indexedByDocument = array_column($this->storage->data, null, 'document');
        if (isset($indexedByDocument[$userData['document']])) {
            throw new UserAPIDuplicatedData();
        }
        $this->storage[$userData['id']] = $userData;
        return true;
    }

    public function getUsers() : array
    {
        return array_values($this->storage->data);
    }

    public function getUserById($id)
    {
        return $this->storage[$id] ?? null;
    }

    public function getConsumers() : array
    {
        return array_filter($this->storage->data, fn($user) => $user['type'] == USER_TYPE_CONSUMER);
    }

    public function getSellers() : array
    {
        return array_filter($this->storage->data, fn($user) => $user['type'] == USER_TYPE_SELLER);
    }

    public function totalOfUsers() : int
    {
        return count($this->storage->data);
    }

    public function totalOfConsumers() : int
    {
        return count(
            $this->getConsumers()
        );
    }

    public function totalOfSellers() : int
    {
        return count(
            $this->getSellers()
        );
    }

    public function deleteUser(string $id) : bool
    {
        if (!isset($this->storage[$id])) {
            return false;
        }
        unset($this->storage[$id]);
        return true;
    }
}