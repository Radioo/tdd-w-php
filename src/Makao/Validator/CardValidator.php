<?php

namespace Makao\Validator;

use Makao\Card;
use Makao\Exception\CardDuplicationException;

class CardValidator {

    /**
     * @throws CardDuplicationException
     */
    public function valid(Card $activeCard, Card $newCard): bool {
        if ($activeCard->getValue() === $newCard->getValue() &&
            $activeCard->getColor() === $newCard->getColor()) {
            throw new CardDuplicationException($newCard);
        }

        return $activeCard->getColor() === $newCard->getColor() ||
            $activeCard->getValue() === $newCard->getValue() ||
            $newCard->getValue() === Card::VALUE_QUEEN ||
            $activeCard->getValue() === Card::VALUE_QUEEN;
    }
}
