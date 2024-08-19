<?php

namespace Tests\Makao\Service;

use Makao\Player;
use Makao\Service\GameService;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase {
    private readonly GameService $gameServiceUnderTest;

    protected function setUp(): void {
        $this->gameServiceUnderTest = new GameService();
    }

    public function testShouldReturnFalseWhenGameIsNotStarted(): void {
        // When
        $actual = $this->gameServiceUnderTest->isStarted();

        // Then
        $this->assertFalse($actual);
    }

    public function testShouldReturnTrueWhenGameIsNotStarted(): void {
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
}
