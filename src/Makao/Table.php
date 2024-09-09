<?php

namespace Makao;

use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\TooManyPlayersAtTheTableException;

class Table
{
    const int MAX_PLAYERS = 4;

    private array $players = [];
    private $currentPlayerIndex = 0;
    private CardCollection $cardDeck;
    private CardCollection $playedCards;
    private ?string $playedCardColor = null;

    public function __construct(CardCollection $cardDeck = null, CardCollection $playedCards = null) {
        $this->cardDeck = $cardDeck ?? new CardCollection();
        $this->playedCards = $playedCards ?? new CardCollection();

        if($playedCards !== null) {
            $this->changePlayedCardColor($this->playedCards->getLastCard()->getColor());
        }
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

    public function addCardCollectionToDeck(CardCollection $cardCollection): self {
        $this->cardDeck->addCollection($cardCollection);

        return $this;
    }

    public function getCurrentPlayer(): Player {
        return $this->players[$this->currentPlayerIndex];
    }

    public function getNextPlayer(): Player {
        return $this->players[$this->currentPlayerIndex + 1] ?? $this->players[0];
    }

    public function getPreviousPlayer(): Player {
        return $this->players[$this->currentPlayerIndex - 1] ?? $this->players[$this->countPlayers() - 1];
    }

    public function finishRound(): void {
        if(++$this->currentPlayerIndex === $this->countPlayers()) {
            $this->currentPlayerIndex = 0;
        }
    }

    public function backRound(): void {
        if(--$this->currentPlayerIndex < 0) {
            $this->currentPlayerIndex = $this->countPlayers() - 1;
        }
    }

    public function getPlayedCardColor(): string {
        if($this->playedCardColor !== null) {
            return $this->playedCardColor;
        }

        throw new CardNotFoundException('No played cards on the table yet!');
    }

    public function addPlayedCard(Card $card): self {
        $this->playedCards->add($card);
        $this->changePlayedCardColor($card->getColor());

        return $this;
    }

    public function changePlayedCardColor(string $color): self {
        $this->playedCardColor = $color;

        return $this;
    }

    public function addPlayedCards(CardCollection $cards): self {
        foreach($cards as $card) {
            $this->addPlayedCard($card);
        }

        return $this;
    }

    /**
     * @return Player[]
     */
    public function getPlayers(): array {
        return $this->players;
    }
}
