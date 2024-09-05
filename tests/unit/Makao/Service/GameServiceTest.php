<?php

namespace Tests\unit\Makao\Service;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\GameException;
use Makao\Player;
use Makao\Service\CardService;
use Makao\Service\GameService;
use Makao\Table;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase {
    private readonly GameService $gameServiceUnderTest;
    private readonly MockObject|CardService $cardServiceMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void {
        $this->cardServiceMock = $this->createMock(CardService::class);

        $this->gameServiceUnderTest = new GameService(new Table(), $this->cardServiceMock);
    }

    public function testShouldReturnFalseWhenGameIsNotStarted(): void {
        // When
        $actual = $this->gameServiceUnderTest->isStarted();

        // Then
        $this->assertFalse($actual);
    }

    public function testShouldReturnTrueWhenGameIsNotStarted(): void {
        // Given
        $this->gameServiceUnderTest->getTable()->addCardCollectionToDeck(new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
        ]));

        $this->gameServiceUnderTest->addPlayers([
            new Player('John'),
            new Player('Andy'),
        ]);

        // When
        $this->gameServiceUnderTest->startGame();

        // Then
        $this->assertTrue($this->gameServiceUnderTest->isStarted());
    }
    
    public function testShouldInitNewGameWithEmptyTable(): void {
        // When
        $table = $this->gameServiceUnderTest->getTable();


        // Then
        $this->assertSame(0, $table->countPlayers());
        $this->assertCount(0, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
    }

    public function testShouldAddPlayersToTheTable(): void {
        // Given
        $players = [
            new Player('John'),
            new Player('Andy'),
            new Player('Tom'),
            new Player('Sam'),
        ];

        // When
        $actual = $this->gameServiceUnderTest->addPlayers($players)->getTable();

        // Then
        $this->assertSame(count($players), $actual->countPlayers());
    }

    /**
     * @throws Exception
     */
    public function testShouldCreateShuffledCardDeck(): void {
        // Given
        $cardCollection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
        ]);

        $shuffledCardCollection = new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),
        ]);

        $this->cardServiceMock->expects($this->once())
            ->method('createDeck')
            ->willReturn($cardCollection);
        $this->cardServiceMock->expects($this->once())
            ->method('shuffle')
            ->with($cardCollection)
            ->willReturn($shuffledCardCollection);

        // When
        $table = $this->gameServiceUnderTest->prepareCardDeck();

        // Then
        $this->assertCount(2, $table->getCardDeck());
        $this->assertCount(0, $table->getPlayedCards());
        $this->assertEquals($shuffledCardCollection, $table->getCardDeck());
    }

    public function testShouldThrowGameExceptionWhenStartingGameWithoutCardDeck(): void {
        // Expect
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('Prepare card deck before game start');

        // When
        $this->gameServiceUnderTest->startGame();
    }

    public function testShouldThrowGameExceptionWhenStartingGameWithoutMinimalPlayers(): void {
        // Given
        $this->gameServiceUnderTest->getTable()->addCardCollectionToDeck(new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
        ]));

        // Expect
        $this->expectException(GameException::class);
        $this->expectExceptionMessage('You need minimum 2 players to start the game');

        // When
        $this->gameServiceUnderTest->startGame();
    }
}
