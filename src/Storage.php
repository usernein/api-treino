<?php
namespace API;

class Storage implements \ArrayAccess
{
    public array $data = [];
    private string $JSONPath;

    public function __construct(string $JSONPath, array $defaultValue = []) {
        $this->JSONPath = $JSONPath;
        if (!file_exists($JSONPath)) {
            file_put_contents($JSONPath, json_encode($defaultValue, JSON_PRETTY_PRINT));
        }
        $this->data = json_decode(file_get_contents($JSONPath), true);
    }

    public function write(string $filename = NULL): int
    {
        $filename ??= $this->JSONPath;
        return file_put_contents($filename, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function &offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
        $this->write();
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
        $this->write();
    }
}