<?php
namespace API\Exceptions;
class UserAPIInvalidData extends UserAPIException {
    public string $error = "invalid_data";
}