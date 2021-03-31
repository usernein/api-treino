<?php
require_once __DIR__.'/../vendor/autoload.php';

use API\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private static string $JSONPath = "user_test.json";
    private static array $initial_values = [
        'id1' => [
            'id' => 'id1',
            'name' => 'Initial Consumer',
            'balance' => 100,
            'document' => 123,
            'type' => 'consumer'
        ],
        'id2' => [
            'id' => 'id2',
            'name' => 'Initial Seller',
            'balance' => 2000,
            'document' => 321,
            'type' => 'seller'
        ]
    ];

    public static function setUpBeforeClass() : void
    {
        if (file_exists(static::$JSONPath)) {
            unlink(static::$JSONPath);
        }
        static::resetJSONFile();
    }

    public static function tearDownAfterClass() : void
    {
        unlink(static::$JSONPath);
    }

    public static function resetJSONFile() : void
    {
        file_put_contents(static::$JSONPath, json_encode(static::$initial_values));
    }

    public function testUserClassCreation() : void
    {
        static::resetJSONFile();
        new User(static::$JSONPath);
        $this->assertEquals(json_encode(static::$initial_values), file_get_contents(static::$JSONPath));
    }

    public function testConsumerRegistering() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $createdID = $user->registerUser([
            "name" => "Test Consumer",
            "document" => 12345678901,
            "type" => "consumer"
        ]);

        $values = json_decode(file_get_contents(static::$JSONPath), true);
        $this->assertArrayHasKey($createdID, $values);
    }

    public function testSellerRegistering() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $createdID = $user->registerUser([
            "name" => "Test Seller",
            "document" => 10987654321,
            "type" => "seller"
        ]);

        $values = json_decode(file_get_contents(static::$JSONPath), true);
        $this->assertArrayHasKey($createdID, $values);
    }

    public function testGetUsers() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $users = $user->getUsers();
        $this->assertCount(2, $users);
        $this->assertContainsOnly('array', $users, true);
        $this->assertArrayHasKey('id1', $users);
        $this->assertArrayHasKey('id2', $users);
    }

    public function testGetConsumers() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $consumers = $user->getConsumers();
        $this->assertCount(1, $consumers);
        $this->assertContainsOnly('array', $consumers, true);
        $this->assertArrayHasKey('id1', $consumers);
    }

    public function testGetsSellers() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $sellers = $user->getSellers();
        $this->assertCount(1, $sellers);
        $this->assertContainsOnly('array', $sellers, true);
        $this->assertArrayHasKey('id2', $sellers);
    }

    public function testGetConsumerByID() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $consumer = $user->getUserById('id1');
        $this->assertEquals('Initial Consumer', $consumer['name']);
    }

    public function testCountUsers() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $this->assertEquals(2, $user->totalOfUsers());
    }

    public function testCountConsumers() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $this->assertEquals(1, $user->totalOfConsumers());
    }

    public function testCountSellers() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $this->assertEquals(1, $user->totalOfSellers());
    }

    public function testDeleteUser() : void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $user->deleteUser('id1');
        $this->assertNull($user->getUserById('id1'));
    }
}