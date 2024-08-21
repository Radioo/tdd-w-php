<?php

namespace Tests\Makao\Validator;

use Makao\Card;
use Makao\Exception\CardDuplicationException;
use Makao\Validator\CardValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CardValidatorTest extends TestCase {
    private readonly CardValidator $cardValidatorUnderTest;

    protected function setUp(): void {
        $this->cardValidatorUnderTest = new CardValidator();
    }

    public static function cardsProvider(): array {
        return [
            'Same colors' => [
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
                true,
            ],
            'Different colors, different values' => [
                new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
                false,
            ],
            'Same values' => [
                new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
                new Card(Card::COLOR_HEART, Card::VALUE_FOUR),
                true,
            ]
        ];
    }

    #[DataProvider('cardsProvider')]
    public function testShouldCheckIfCardsAreValid(
        Card $activeCard,
        Card $newCard,
        bool $expected
    ): void {
        // Given
        $cardValidator = new CardValidator();

        // When
        $actual = $this->cardValidatorUnderTest->valid($activeCard, $newCard);

        // Then
        $this->assertEquals($actual, $expected);
    }

    public function testShouldThrowCardDuplicationExceptionWhenValidCardsAreTheSame(): void {
        // Expect
        $this->expectException(CardDuplicationException::class);
        $this->expectExceptionMessage('Got same cards: 5 spade');

        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FIVE);

        // When
        $this->cardValidatorUnderTest->valid($card, $card);

    }
}
