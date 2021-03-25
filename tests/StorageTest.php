<?php
namespace Tests;
require_once __DIR__.'/../vendor/autoload.php';

use API\Storage;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase {
    private static string $JSONPath = "storage_test.json";
    private static array $initial_values = [
      'key_get' => 'value_get',
      'ket_set' => 'old_value',
      'key_unset' => 'value_unset'
    ];

    public static function setUpBeforeClass() : void
    {
        if (file_exists(static::$JSONPath)) {
            unlink(static::$JSONPath);
        }
        file_put_contents(static::$JSONPath, json_encode(static::$initial_values));
    }

    public static function tearDownAfterClass() : void
    {
        unlink(static::$JSONPath);
    }

    public function testStorageCreation() : void
    {
        new Storage(static::$JSONPath);
        $this->assertEquals(json_encode(static::$initial_values), file_get_contents(static::$JSONPath));
    }

    public function testStorageSet() : void
    {
        $storage = new Storage(static::$JSONPath);
        $storage->offsetSet("key_set", "new_value");

        $values = json_decode(file_get_contents(static::$JSONPath), true);
        $this->assertEquals("new_value", $values["key_set"]);
    }

    public function testStorageGet() : void
    {
        $storage = new Storage(static::$JSONPath);
        $this->assertEquals(
            "value_get",
            $storage->offsetGet("key_get")
        );
    }

    public function testStorageUnset() : void
    {
        $storage = new Storage(static::$JSONPath);
        $storage->offsetUnset("key_unset");

        $values = json_decode(file_get_contents(static::$JSONPath), true);
        $this->assertNotTrue(array_key_exists('key_unset', $values));
    }

    public function testStorageExists() : void
    {
        $storage = new Storage(static::$JSONPath);
        $this->assertTrue($storage->offsetExists("key_get"));
    }
}