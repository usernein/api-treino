<?php
namespace API\Exceptions;

class TransactionAPINonexistentReceiver extends TransactionAPIException
{
    public string $error = "receiver_not_found";
}