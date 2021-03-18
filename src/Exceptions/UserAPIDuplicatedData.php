<?php
namespace API\Exceptions;
class UserAPIDuplicatedData extends UserAPIException {
    public string $error = "duplicated_data";
}