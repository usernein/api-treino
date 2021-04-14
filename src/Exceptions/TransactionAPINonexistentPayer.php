<?php
namespace API\Exceptions;

class TransactionAPINonexistentPayer extends TransactionAPIException
{
    public string $error = "payer_not_found";
}