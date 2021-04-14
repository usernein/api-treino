<?php
require_once __DIR__.'/../vendor/autoload.php';

use API\User;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    private static string $JSONPath = __DIR__."/transaction_test.json";
    private static array $initial_values = [
        'id0' => [
            'id' => 'id0',
            'name' => 'Initial Consumer 1',
            'balance' => 100,
            'document' => 000,
            'type' => 'consumer'
        ],
        'id1' => [
            'id' => 'id1',
            'name' => 'Initial Consumer 2',
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
        ],
    ];

    public static function setUpBeforeClass(): void
    {
        if (file_exists(static::$JSONPath)) {
            unlink(static::$JSONPath);
        }
        static::resetJSONFile();
    }

    public static function tearDownAfterClass(): void
    {
        unlink(static::$JSONPath);
    }

    public static function resetJSONFile(): void
    {
        file_put_contents(static::$JSONPath, json_encode(static::$initial_values));
    }

    public function testP2B(): void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $transaction = $user->createTransaction(25, "id0", "id2");
        $transaction->execute();

        $payer = $user->getUserById("id0");
        $this->assertEquals(75, $payer["balance"]);

        $receiver = $user->getUserById("id2");
        $this->assertEquals(2025, $receiver["balance"]);
    }

    public function testP2P(): void
    {
        static::resetJSONFile();
        $user = new User(static::$JSONPath);
        $transaction = $user->createTransaction(25, "id0", "id1");
        $transaction->execute();


        $payer = $user->getUserById("id0");
        $this->assertEquals(75, $payer["balance"]);

        $receiver = $user->getUserById("id1");
        $this->assertEquals(125, $receiver["balance"]);
    }
}