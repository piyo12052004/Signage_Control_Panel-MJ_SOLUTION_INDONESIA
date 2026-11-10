<?php

namespace App\Exceptions\Identity;

use RuntimeException;

class RefreshTokenReuseDetectedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Refresh token reuse detected.');
    }
}
