<?php

namespace Makao\Service;

use Makao\Player;
use Makao\Table;

class GameService {
    private Table $table;
    private bool $isStarted = false;

    public function __construct(Table $table = null) {
        $this->table = $table ?? new Table();
    }

    public function isStarted(): bool {
        return $this->isStarted;
    }

    public function getTable(): Table {
        return $this->table;
    }

    /**
     * @param Player[] $players
     * @return $this
     */
    public function addPlayers(array $players): self {
        foreach($players as $player) {
            $this->table->addPlayer($player);
        }

        return $this;
    }

    public function startGame(): void {
        $this->isStarted = true;
    }
}
