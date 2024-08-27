<?php

namespace Makao\Service;

use Makao\Card;
use Makao\Table;

class CardActionService {
    private Table $table;

    public function __construct(Table $table) {
        $this->table = $table;
    }

    public function afterCard(Card $card): void {
        $this->table->finishRound();

        switch($card->getValue()) {
            case Card::VALUE_TWO:
                $this->cardTwoAction();
                break;
            default:
                break;
        }
    }

    private function cardTwoAction(): void {
        $this->table->getCurrentPlayer()->takeCards($this->table->getCardDeck(), 2);
        $this->table->finishRound();
    }
}
