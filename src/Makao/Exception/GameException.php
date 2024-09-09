<?php

namespace Makao\Exception;

use RuntimeException;
use Throwable;

class GameException extends RuntimeException {
    public function __construct(string $message = "", Throwable $previous = null) {
        if($previous !== null) {
            $message .= ' Issue: ' . $previous->getMessage();
        }

        parent::__construct($message, 0, $previous);
    }
}
