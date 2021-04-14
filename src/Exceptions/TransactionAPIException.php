<?php
namespace API\Exceptions;

class TransactionAPIException extends \Exception
{
    public string $error = "generic";
}