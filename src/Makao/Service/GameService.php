<?php

namespace Makao\Service;

use Exception;
use Makao\Exception\GameException;
use Makao\Player;
use Makao\Table;

class GameService {
    private const int MIN_PLAYERS = 2;
    private const int STARTING_CARDS = 5;

    private Table $table;
    private CardService $cardService;
    private bool $isStarted = false;

    public function __construct(Table $table, CardService $cardService) {
        $this->table = $table;
        $this->cardService = $cardService;
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
        $this->validateBeforeStartGame();

        try {
            $this->isStarted = true;

            $cardDeck = $this->table->getCardDeck();
            $card = $this->cardService->pickFirstNoActionCard($this->table->getCardDeck());
            $this->table->addPlayedCard($card);

            $players = $this->table->getPlayers();
            foreach($players as $player) {
                $player->takeCards($cardDeck, self::STARTING_CARDS);
            }
        }
        catch(Exception $e) {
            throw new GameException('The game needs help!', $e);
        }
    }

    public function prepareCardDeck(): Table {
        $cardCollection = $this->cardService->createDeck();
        $cardDeck = $this->cardService->shuffle($cardCollection);

        return $this->table->addCardCollectionToDeck($cardDeck);
    }

    public function validateBeforeStartGame(): void {
        if($this->table->getCardDeck()->count() === 0) {
            throw new GameException('Prepare card deck before game start');
        }

        if($this->table->countPlayers() < self::MIN_PLAYERS) {
            throw new GameException("You need minimum " . self::MIN_PLAYERS . " players to start the game");
        }
    }
}
