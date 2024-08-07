<?php

namespace Makao\Collection;

use Countable;
use Makao\Card;
use Makao\Exception\CardNotFoundException;
use Override;

class CardCollection implements Countable
{
    private array $cards = [];

    #[Override]
    public function count(): int {
        return count($this->cards);
    }

    public function add(Card $card): self {
        $this->cards[] = $card;

        return $this;
    }

    public function pickCard(): Card {
        if(empty($this->cards)) {
            throw new CardNotFoundException('You can not pick a card from empty CardCollection');
        }
    }
}
