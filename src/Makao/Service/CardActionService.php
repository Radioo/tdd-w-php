<?php

namespace Makao\Service;

use Makao\Card;
use Makao\Exception\CardNotFoundException;
use Makao\Table;

class CardActionService {
    private Table $table;
    private int $actionCount = 0;

    public function __construct(Table $table) {
        $this->table = $table;
    }

    public function afterCard(Card $card, ?string $request = null): void {
        $this->table->finishRound();

        switch($card->getValue()) {
            case Card::VALUE_TWO:
                $this->takingCards(Card::VALUE_TWO, 2);
                break;
            case Card::VALUE_THREE:
                $this->takingCards(Card::VALUE_THREE, 3);
                break;
            case Card::VALUE_FOUR:
                $this->skipRound();
                break;
            case Card::VALUE_JACK:
                $this->requestingCardValue($request);
                break;
            default:
                break;
        }
    }

    private function takingCards(string $cardValue, int $cardsToGet): void {
        $this->actionCount += $cardsToGet;
        $player = $this->table->getCurrentPlayer();

        try {
            $cards = $player->pickCardsByValue($cardValue);
            $this->table->getPlayedCards()->addCollection($cards);
            $this->table->finishRound();
            $this->takingCards($cardValue, $cardsToGet);
        }
        catch(CardNotFoundException) {
            $this->playerTakeCards($this->actionCount);

        }
    }

    private function playerTakeCards(int $count): void {
        $this->table->getCurrentPlayer()->takeCards($this->table->getCardDeck(), $count);
        $this->table->finishRound();
    }

    private function skipRound(): void {
        ++$this->actionCount;
        $player = $this->table->getCurrentPlayer();

        try {
            $card = $player->pickCardByValue(Card::VALUE_FOUR);
            $this->table->getPlayedCards()->add($card);
            $this->table->finishRound();
            $this->skipRound();
        }
        catch(CardNotFoundException) {
            $player->addRoundToSkip($this->actionCount - 1);
            $this->table->finishRound();
        }
    }

    private function requestingCardValue(?string $request): void {
        $iterations = $this->table->countPlayers();
        for($i = 0; $i < $iterations; $i++) {
            $player = $this->table->getCurrentPlayer();

            try {
                $cards = $player->pickCardsByValue($request);
                $this->table->getPlayedCards()->addCollection($cards);
            }
            catch(CardNotFoundException) {
                $player->takeCards($this->table->getCardDeck());
            }

            $this->table->finishRound();
        }
    }
}
