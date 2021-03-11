<?php
/** TODO
 * 3. Reduzir registerUser
 * 6. Trocar require por uso de namespaces
 * 7. Criar endpoints de seller (lojistas)
 *
 * 8. Teste unitário
 */
require_once 'storage.php';
class User
{
    private Storage $storage;

    public function __construct()
    {
        $this->storage = new Storage('users.json');
    }

    public function registerUser(array $userFields) : bool
    {
        if (!isset($userFields['name']) || !isset($userFields['document'])) {
            throw new UserAPINotEnoughData();
        }

        $userData = [
            'id' => uniqid(),
            'name' => $userFields['name'],
            'balance' => 0,
            'document' => (int)$userFields['document']
        ];
        if (!$userData['name'] or !is_string($userData["name"])) {
            throw new UserAPIInvalidData("name");
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

    public function totalOfUsers() : int
    {
        return count($this->storage->data);
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

class UserAPIException extends Exception {}
class UserAPINotEnoughData extends UserAPIException {}
class UserAPIDuplicatedData extends UserAPIException {}
class UserAPIInvalidData extends UserAPIException {}