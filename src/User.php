<?php
namespace API;

use API\Exceptions\{UserAPINotEnoughData, UserAPIDuplicatedData, UserAPIInvalidData, UserAPINonexistentId};

define("USER_TYPE_CONSUMER", "consumer");
define("USER_TYPE_SELLER", "seller");

class User
{
    private Storage $storage;

    public function __construct(string $JSONPath = 'users.json')
    {
        $this->storage = new Storage($JSONPath);
    }

    public function registerUser(array $userFields): string
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
        return $userData['id'];
    }

    public function getUsers(): array
    {
        return $this->storage->data;
    }

    public function getUserById($id)
    {
        if (!isset($this->storage[$id])) {
            throw new UserAPINonexistentId();
        }
        return $this->storage[$id];
    }

    public function getConsumers(): array
    {
        return array_filter($this->storage->data, fn($user) => $user['type'] == USER_TYPE_CONSUMER);
    }

    public function getSellers(): array
    {
        return array_filter($this->storage->data, fn($user) => $user['type'] == USER_TYPE_SELLER);
    }

    public function totalOfUsers(): int
    {
        return count($this->storage->data);
    }

    public function totalOfConsumers(): int
    {
        return count(
            $this->getConsumers()
        );
    }

    public function totalOfSellers(): int
    {
        return count(
            $this->getSellers()
        );
    }

    public function deleteUser(string $id): void
    {
        if (!isset($this->storage[$id])) {
            throw new UserAPINonexistentId();
        }
        unset($this->storage[$id]);
    }

    public function createTransaction(float $value, string $payerId, string $receiverId)
    {
        return new Transaction($this, $value, $payerId, $receiverId);
    }

    public function increaseUserBalance(string $id, float $value)
    {
        if (!isset($this->storage[$id])) {
            throw new UserAPINonexistentId();
        }
        $this->storage->data[$id]["balance"] += $value;
    }

    public function decreaseUserBalance(string $id, float $value)
    {
        if (!isset($this->storage[$id])) {
            throw new UserAPINonexistentId();
        }
        $this->storage->data[$id]["balance"] -= $value;
    }
}