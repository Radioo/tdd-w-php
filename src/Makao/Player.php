<?php

namespace Makao;

use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;

class Player {
    private const string MAKAO = 'Makao';

    private string $name;
    private CardCollection $cardCollection;
    private int $roundsToSkip = 0;

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

    public function pickCard(int $index = 0): Card {
        return $this->getCards()->pickCard($index);
    }

    public function takeCards(CardCollection $cardCollection, int $count = 1): self {
        for($i = 0; $i < $count; $i++) {
            $this->cardCollection->add($cardCollection->pickCard());
        }

        return $this;
    }

    public function sayMakao(): string {
        return self::MAKAO;
    }

    public function pickCardByValue(string $value): Card {
        foreach($this->cardCollection as $index => $card) {
            if ($card->getValue() === $value) {
                return $this->cardCollection->pickCard($index);
            }
        }

        throw new CardNotFoundException("Player John does not have card with value " . $value);
    }

    public function getRoundsToSkip(): int {
        return $this->roundsToSkip;
    }

    public function canPlayRound(): bool {
        return $this->getRoundsToSkip() === 0;
    }

    public function addRoundToSkip(int $count = 1): self {
        $this->roundsToSkip += $count;

        return $this;
    }

    public function skipRound(): self {
        $this->roundsToSkip--;

        return $this;
    }

    public function pickCardsByValue(string $cardValue): CardCollection {
        $collection = new CardCollection();

        try {
            while($card = $this->pickCardByValue($cardValue)) {
                $collection->add($card);
            }
        }
        catch(CardNotFoundException $e) {
            if($collection->count() === 0) {
                throw $e;
            }
        }

        return $collection;
    }
}
