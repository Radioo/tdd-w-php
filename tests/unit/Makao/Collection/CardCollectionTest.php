<?php

namespace Tests\unit\Makao\Collection;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\MethodNotAllowedException;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertSame;

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
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);

        // When
        $this->cardCollectionUnderTest->add($card);

        // Then
        $this->assertCount(1, $this->cardCollectionUnderTest);
    }

    public function testShouldAddNewCardsInChainToCardCollection(): void {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE);

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

    public function testShouldIterableOnCardCollection(): void {
        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);

        // When & Then
        $this->cardCollectionUnderTest->add($card);

        $this->assertTrue($this->cardCollectionUnderTest->valid());
        $this->assertSame($card, $this->cardCollectionUnderTest->current());
        $this->assertSame(0, $this->cardCollectionUnderTest->key());

        $this->cardCollectionUnderTest->next();
        $this->assertFalse($this->cardCollectionUnderTest->valid());
        $this->assertSame(1, $this->cardCollectionUnderTest->key());

        $this->cardCollectionUnderTest->rewind();
        $this->assertTrue($this->cardCollectionUnderTest->valid());
        $this->assertSame(0, $this->cardCollectionUnderTest->key());
    }

    public function testShouldGetFirstCardFromCardCollectionAndRemoveThisCardFromDeck(): void {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE);

        $this->cardCollectionUnderTest
            ->add($firstCard)
            ->add($secondCard);

        // When
        $actual = $this->cardCollectionUnderTest->pickCard();

        // Then
        $this->assertCount(1, $this->cardCollectionUnderTest);
        $this->assertSame($firstCard, $actual);
        $this->assertSame($secondCard, $this->cardCollectionUnderTest[0]);
    }

    public function testShouldCardNotFoundExceptionWhenIPickedAllCardsFromCardCollection(): void {
        // Expect
        $this->expectException(CardNotFoundException::class);
        $this->expectExceptionMessage('You can not pick a card from empty CardCollection');

        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE);

        $this->cardCollectionUnderTest
            ->add($firstCard)
            ->add($secondCard);

        // When
        $actual = $this->cardCollectionUnderTest->pickCard();
        assertSame($firstCard, $actual);

        $actual = $this->cardCollectionUnderTest->pickCard();
        assertSame($secondCard, $actual);

        $this->cardCollectionUnderTest->pickCard();
    }

    public function testShouldReturnChosenCardPickedFromCollection(): void {
        // Given
        $firstCard = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);
        $secondCard = new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE);

        $this->cardCollectionUnderTest
            ->add($firstCard)
            ->add($secondCard);

        // When
        $actual = $this->cardCollectionUnderTest->pickCard(1);

        // Then
        $this->assertSame($secondCard, $actual);
    }

    public function testShouldThrowMethodNotAllowedExceptionWhenYouTryToAddACardToCollectionAsArray(): void {
        // Expect
        $this->expectException(MethodNotAllowedException::class);
        $this->expectExceptionMessage('You can not add a card to CardCollection as array. Use addCard() method!');

        // Given
        $card = new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT);

        // When
        $this->cardCollectionUnderTest[] = $card;
    }

    public function testShouldReturnCollectionAsArray(): void {
        // Given
        $cards = [
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
        ];

        // When
        $actual = new CardCollection($cards);

        // Then
        $this->assertEquals($cards, $actual->toArray());
    }

    public function testShouldAddCardCollectionToCardCollection(): void {
        // Given
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
        ]);

        // When
        $actual = $this->cardCollectionUnderTest->addCollection($collection);

        // Then
        $this->assertEquals($collection, $actual);
    }
}
