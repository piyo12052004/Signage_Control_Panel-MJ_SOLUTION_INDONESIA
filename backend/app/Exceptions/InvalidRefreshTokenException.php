<?php

namespace App\Exceptions\Identity;

use RuntimeException;

class InvalidRefreshTokenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid refresh token.');
    }
}
