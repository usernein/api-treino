<?php
namespace API\Exceptions;

class TransactionAPIInsufficientBalance extends TransactionAPIException
{
    public string $error = "insufficient_balance";
}