<?php

namespace Makao;

use Makao\Collection\CardCollection;
use Makao\Exception\TooManyPlayersAtTheTableException;

class Table
{
    const int MAX_PLAYERS = 4;

    private array $players = [];
    private CardCollection $cardDeck;
    private CardCollection $playedCards;

    public function __construct(CardCollection $cardDeck = null) {
        $this->cardDeck = $cardDeck ?? new CardCollection();
        $this->playedCards = new CardCollection();
    }

    public function countPlayers(): int {
        return count($this->players);
    }

    public function addPlayer(Player $player): void {
        if($this->countPlayers() === self::MAX_PLAYERS) {
            throw new TooManyPlayersAtTheTableException(self::MAX_PLAYERS);
        }

        $this->players[] = $player;
    }

    public function getPlayedCards(): CardCollection {
        return $this->playedCards;
    }

    public function getCardDeck(): CardCollection {
        return $this->cardDeck;
    }
}
