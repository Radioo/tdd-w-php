<?php

namespace Tests\unit\Makao;

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
        $actual = $player->takeCards($cardCollection)->getCards();

        // Then
        $this->assertCount(0, $cardCollection);
        $this->assertCount(1, $actual);
        $this->assertSame($card, $actual[0]);
    }

    public function testShouldAllowPlayerToTakeManyCardsFromCardCollection(): void {
        // Given
        $firstCard = new Card(Card::COLOR_HEART, Card::VALUE_ACE);
        $secondCard = new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT);
        $thirdCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_KING);
        $cardCollection = new CardCollection([$firstCard, $secondCard, $thirdCard]);

        $player = new Player('John');

        // When
        $actual = $player->takeCards($cardCollection, 2);

        // Then
        $this->assertCount(1, $cardCollection);
        $this->assertCount(2, $actual->getCards());
        $this->assertSame($firstCard, $actual->pickCard());
        $this->assertSame($secondCard, $actual->pickCard());
        $this->assertSame($thirdCard, $cardCollection->pickCard());
    }

    public function testShouldAllowPickingSpecificCardFromPlayerCardCollection(): void {
        // Given
        $firstCard = new Card(Card::COLOR_HEART, Card::VALUE_ACE);
        $secondCard = new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT);
        $thirdCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_KING);

        $player = new Player('John', new CardCollection([
            $firstCard, $secondCard, $thirdCard
        ]));

        // When
        $actual = $player->pickCard(2);

        // Then
        $this->assertSame($thirdCard, $actual);
    }

    public function testShouldAllowPlayerToSayMakao(): void {
        // Given
        $player = new Player('John');

        // When
        $actual = $player->sayMakao();

        // Then
        $this->assertEquals('Makao', $actual);
    }
}
