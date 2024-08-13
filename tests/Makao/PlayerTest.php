<?php

namespace Tests\Makao;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase {
    public function testShouldWritePlayerName(): void {
        // Given
        $player = new Player('John');

        // When
        ob_start();
        echo $player;
        $actual = ob_get_clean();

        // Then
        $this->assertSame('John', $actual);
    }

    public function testShouldReturnPlayerCardCollection(): void {
        // Given
        $cardCollection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),
        ]);
        $player = new Player('John', $cardCollection);

        // When
        $actual = $player->getCards();

        // Then
        $this->assertSame($cardCollection, $actual);
    }

    public function testShouldAllowPlayerToTakeACardFromDeck(): void {
        // Given
        $card = new Card(Card::COLOR_HEART, Card::VALUE_ACE);
        $cardCollection = new CardCollection([$card]);
        $player = new Player('John');

        // When
        $actual = $player->takeCard($cardCollection)->getCards();

        // Then
        $this->assertCount(0, $cardCollection);
        $this->assertCount(1, $actual);
        $this->assertSame($card, $actual[0]);
    }
}
