<?php
namespace API\Exceptions;

class UserAPINonexistentId extends UserAPIException
{
    public string $error = "user_id_not_found";
}