<?php

namespace Tests\Makao\Collection;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use PHPUnit\Framework\TestCase;

class CardCollectionTest extends TestCase
{
    private readonly CardCollection $cardCollectionUnderTest;

    protected function setUp(): void {
        $this->cardCollectionUnderTest = new CardCollection();
    }

    public function testShouldReturnZeroOnEmptyCollection(): void {
        // Then
        $this->assertCount(0, $this->cardCollectionUnderTest);
    }

    public function testShouldAddNewCardToCardCollection(): void {
        // Given
        $card = new Card();

        // When
        $this->cardCollectionUnderTest->add($card);

        // Then
        $this->assertCount(1, $this->cardCollectionUnderTest);
    }

    public function testShouldAddNewCardsInChainToCardCollection(): void {
        // Given
        $firstCard = new Card();
        $secondCard = new Card();

        // When
        $this->cardCollectionUnderTest
            ->add($firstCard)
            ->add($secondCard);

        // Then
        $this->assertCount(2, $this->cardCollectionUnderTest);
    }

    public function testShouldThrowCardNotFoundExceptionWhenITryToPickACardFromEmptyCardCollection(): void {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('You can not pick a card from empty CardCollection');

        // When
        $this->cardCollectionUnderTest->pickCard();
    }
}
