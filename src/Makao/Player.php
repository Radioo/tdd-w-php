<?php

namespace Makao;

use Makao\Collection\CardCollection;

class Player {
    private string $name;
    private CardCollection $cardCollection;

    public function __construct(string $name, ?CardCollection $cardCollection = null) {
        $this->name = $name;
        $this->cardCollection = $cardCollection ?? new CardCollection();
    }

    public function __toString(): string {
        return $this->name;
    }

    public function getCards(): CardCollection {
        return $this->cardCollection;
    }

    public function takeCard(CardCollection $cardCollection): self {
        $this->cardCollection->add($cardCollection->pickCard());

        return $this;
    }
}
