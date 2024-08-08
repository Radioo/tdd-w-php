<?php

namespace Makao\Collection;

use ArrayAccess;
use Countable;
use Iterator;
use Makao\Card;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\MethodNotAllowedException;
use Override;

class CardCollection implements Countable, Iterator, ArrayAccess
{
    private const int FIRST_CARD_INDEX = 0;

    private array $cards = [];
    private int $position = self::FIRST_CARD_INDEX;

    #[Override]
    public function count(): int {
        return count($this->cards);
    }

    public function add(Card $card): self {
        $this->cards[] = $card;

        return $this;
    }

    public function pickCard(): Card {
        if(empty($this->cards)) {
            throw new CardNotFoundException('You can not pick a card from empty CardCollection');
        }

        $pickedCard = $this->offsetGet(self::FIRST_CARD_INDEX);
        $this->offsetUnset(self::FIRST_CARD_INDEX);
        $this->cards = array_values($this->cards);

        return $pickedCard;
    }

    #[Override]
    public function current(): ?Card {
        return $this->cards[$this->position];
    }

    #[Override]
    public function next(): void {
        ++$this->position;
    }

    #[Override]
    public function key(): int {
        return $this->position;
    }

    #[Override]
    public function valid(): bool {
        return isset($this->cards[$this->position]);
    }

    #[Override]
    public function rewind(): void {
        $this->position = self::FIRST_CARD_INDEX;
    }

    #[Override]
    public function offsetExists(mixed $offset): bool {
        return isset($this->cards[$offset]);
    }

    #[Override]
    public function offsetGet(mixed $offset): Card {
        return $this->cards[$offset];
    }

    /**
     * @throws MethodNotAllowedException
     */
    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void {
        throw new MethodNotAllowedException('You can not add a card to CardCollection as array. Use addCard() method!');
    }

    #[Override]
    public function offsetUnset(mixed $offset): void {
        unset($this->cards[$offset]);
    }
}
