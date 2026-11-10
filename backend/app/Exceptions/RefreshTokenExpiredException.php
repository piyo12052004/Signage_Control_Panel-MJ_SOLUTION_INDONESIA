<?php

namespace App\Exceptions\Identity;

use RuntimeException;

class RefreshTokenExpiredException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Refresh token has expired.');
    }
}
