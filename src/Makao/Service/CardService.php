<?php

namespace Makao\Service;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;

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

    public function pickFirstNoActionCard(CardCollection $collection): Card {
        $firstCard = null;
        $card = $collection->pickCard();

        while($this->isAction($card) && $firstCard !== $card) {
            $collection->add($card);

            if($firstCard === null) {
                $firstCard = $card;
            }

            $card = $collection->pickCard();
        }

        if($this->isAction($card)) {
            throw new CardNotFoundException('No regular cards in collection');
        }

        return $card;
    }

    private function isAction(Card $card): bool {
        return in_array($card->getValue(), [
            Card::VALUE_TWO,
            Card::VALUE_THREE,
            Card::VALUE_FOUR,
            Card::VALUE_JACK,
            Card::VALUE_QUEEN,
            Card::VALUE_KING,
            Card::VALUE_ACE,
        ], true);
    }
}
