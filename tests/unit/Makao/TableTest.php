<?php

namespace Tests\unit\Makao;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\TooManyPlayersAtTheTableException;
use Makao\Player;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    private Table $tableUnderTest;

    public function setUp(): void {
        $this->tableUnderTest = new Table();
    }

    public function testShouldCreateEmptyTable(): void {
        // Given
        $expected = 0;

        // When
        $actual = $this->tableUnderTest->countPlayers();

        // Then
        $this->assertSame($expected, $actual);
    }

    public function testShouldAddOnePlayerToTable(): void {
        // Given
        $expected = 1;
        $this->tableUnderTest = new Table();
        $player = new Player('John');

        // When
        $this->tableUnderTest->addPlayer($player);
        $actual = $this->tableUnderTest->countPlayers();

        // Then
        $this->assertSame($expected, $actual);
    }

    public function testShouldReturnCountWhenIAddManyPlayers(): void {
        // Given
        $expected = 2;
        $this->tableUnderTest = new Table();

        // When
        $this->tableUnderTest->addPlayer(new Player('John'));
        $this->tableUnderTest->addPlayer(new Player('Andy'));
        $actual = $this->tableUnderTest->countPlayers();

        // Then
        $this->assertSame($expected, $actual);
    }

    public function testShouldThrowTooManyPlayersAtTheTableExceptionWhenITryToAddMoreThanFourPlayers(): void {
        // Expect
        $this->expectException(TooManyPlayersAtTheTableException::class);
        $this->expectExceptionMessage('Max capacity is 4 players!');

        // When
        $this->tableUnderTest->addPlayer(new Player('John'));
        $this->tableUnderTest->addPlayer(new Player('Mike'));
        $this->tableUnderTest->addPlayer(new Player('Andy'));
        $this->tableUnderTest->addPlayer(new Player('Tom'));
        $this->tableUnderTest->addPlayer(new Player('Jerry'));
    }

    public function testShouldReturnEmptyCardCollectionForPlayedCard(): void {
        // When
        $actual = $this->tableUnderTest->getPlayedCards();

        // Then
        $this->assertCount(0, $actual);
    }

    public function testShouldPutCardDeckOnTable(): void {
        // Given
        $cards = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
        ]);

        // When
        $table = new Table($cards);
        $actual = $table->getCardDeck();

        // Then
        $this->assertSame($cards, $actual);
    }

    public function testShouldAddCardCollectionToCardDeckOnTable(): void {
        // Given
        $cardCollection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
        ]);

        // When
        $actual = $this->tableUnderTest->addCardCollectionToDeck($cardCollection);

        // Then
        $this->assertEquals($cardCollection, $actual->getCardDeck());
    }

    public function testShouldReturnCurrentPlayer(): void {
        // Given
        $player1 = new Player('John');
        $player2 = new Player('Andy');
        $player3 = new Player('Tom');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);

        // When
        $actual = $this->tableUnderTest->getCurrentPlayer();

        // Then
        $this->assertSame($player1, $actual);
    }

    public function testShouldReturnNextPlayer(): void {
        // Given
        $player1 = new Player('John');
        $player2 = new Player('Andy');
        $player3 = new Player('Tom');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);

        // When
        $actual = $this->tableUnderTest->getNextPlayer();

        // Then
        $this->assertSame($player2, $actual);
    }

    public function testShouldReturnPreviousPlayer(): void {
        // Given
        $player1 = new Player('John');
        $player2 = new Player('Andy');
        $player3 = new Player('Tom');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);

        // When
        $actual = $this->tableUnderTest->getPreviousPlayer();

        // Then
        $this->assertSame($player3, $actual);
    }

    public function testShouldSwitchCurrentPlayerWhenRoundIsFinished(): void {
        // Given
        $player1 = new Player('John');
        $player2 = new Player('Andy');
        $player3 = new Player('Tom');

        $this->tableUnderTest->addPlayer($player1);
        $this->tableUnderTest->addPlayer($player2);
        $this->tableUnderTest->addPlayer($player3);

        // When & Then
        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player2, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player1, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player3, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player1, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getPreviousPlayer());

        $this->tableUnderTest->finishRound();

        $this->assertSame($player1, $this->tableUnderTest->getCurrentPlayer());
        $this->assertSame($player2, $this->tableUnderTest->getNextPlayer());
        $this->assertSame($player3, $this->tableUnderTest->getPreviousPlayer());
    }
}
