<?php
namespace API\Exceptions;
class UserAPINotEnoughData extends UserAPIException {
    public string $error = "insufficient_data";
}