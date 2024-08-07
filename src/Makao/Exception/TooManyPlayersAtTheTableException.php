<?php

namespace Makao\Exception;

use RuntimeException;
use Throwable;

class TooManyPlayersAtTheTableException extends RuntimeException
{
    public function __construct(int $maxPlayers, int $code = 0, ?Throwable $previous = null) {
        parent::__construct("Max capacity is $maxPlayers players!", $code, $previous);
    }
}
