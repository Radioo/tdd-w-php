<?php

namespace Makao\Exception;

use Exception;
use Makao\Card;
use Throwable;

class CardDuplicationException extends Exception {
    public function __construct(Card $card, int $code = 0, Throwable $previous = null) {
        $message = "Got same cards: {$card->getValue()} {$card->getColor()}";
        parent::__construct($message, $code, $previous);
    }
}
