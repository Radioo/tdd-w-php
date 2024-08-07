<?php

namespace Makao;

use Makao\Exception\TooManyPlayersAtTheTableException;

class Table
{
    const int MAX_PLAYERS = 4;

    private array $players = [];

    public function countPlayers(): int {
        return count($this->players);
    }

    public function addPlayer(Player $player): void {
        if($this->countPlayers() === self::MAX_PLAYERS) {
            throw new TooManyPlayersAtTheTableException(self::MAX_PLAYERS);
        }

        $this->players[] = $player;
    }
}
