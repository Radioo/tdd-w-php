<?php

namespace Tests\Makao;

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
        $player = new Player();

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
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $actual = $this->tableUnderTest->countPlayers();

        // Then
        $this->assertSame($expected, $actual);
    }

    public function testShouldThrowTooManyPlayersAtTheTableExceptionWhenITryToAddMoreThanFourPlayers(): void {
        // Expect
        $this->expectException(TooManyPlayersAtTheTableException::class);
        $this->expectExceptionMessage('Max capacity is 4 players!');

        // When
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
        $this->tableUnderTest->addPlayer(new Player());
    }
}
