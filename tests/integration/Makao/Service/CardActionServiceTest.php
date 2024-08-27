<?php

namespace Tests\integration\Makao\Service;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use Makao\Service\CardActionService;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class CardActionServiceTest extends TestCase {
    public function testShouldGiveNextPlayerTwoCardsWhenCardTwoWasDropped(): void {
        // Given
        $playedCard = new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT)
        ]);

        $deck = new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN),
            new Card(Card::COLOR_HEART, Card::VALUE_SEVEN),
        ]);

        $player1 = new Player('Andy');
        $player2 = new Player('Tom');
        $player3 = new Player('John');

        $table = new Table($deck, $playedCard);
        $table->addPlayer($player1);
        $table->addPlayer($player2);
        $table->addPlayer($player3);

        $cardActionServiceUnderTest = new CardActionService($table);

        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);

        // When
        $cardActionServiceUnderTest->afterCard($card);

        // Then
        $this->assertCount(2, $player2->getCards());
        $this->assertSame($player3, $table->getCurrentPlayer());
    }
}
