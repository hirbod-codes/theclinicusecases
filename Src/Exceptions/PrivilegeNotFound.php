<?php

namespace TheClinicUseCases\Exceptions;

class PrivilegeNotFound extends \LogicException
{
    public function __construct(
        string $message = "There is no such privilege.",
        int $code = 500
    ) {
        $this->message = $message;
        $this->code = $code;
    }
}
