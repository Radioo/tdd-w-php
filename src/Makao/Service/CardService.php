<?php

namespace Makao\Service;

use Makao\Card;
use Makao\Collection\CardCollection;

class CardService {
    private ShuffleService $shuffleService;

    public function __construct(ShuffleService $shuffleService) {
        $this->shuffleService = $shuffleService;
    }

    public function createDeck(): CardCollection {
        $deck = new CardCollection();

        foreach(Card::values() as $value) {
            foreach(Card::colors() as $color) {
                $deck->add(new Card($color, $value));
            }
        }

        return $deck;
    }

    public function shuffle(CardCollection $collection): CardCollection {
        return new CardCollection($this->shuffleService->shuffle($collection->toArray()));
    }
}
