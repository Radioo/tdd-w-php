<?php

namespace Tests\integration\Makao\Service;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Player;
use Makao\Service\CardActionService;
use Makao\Table;
use PHPUnit\Framework\TestCase;

class CardActionServiceTest extends TestCase {
    private Player $player1;
    private Player $player2;
    private Player $player3;
    private Table $table;
    private CardActionService $serviceUnderTest;

    protected function setUp(): void {
        $playedCard = new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT)
        ]);

        $deck = new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_FIVE),
            new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
            new Card(Card::COLOR_SPADE, Card::VALUE_SIX),
            new Card(Card::COLOR_HEART, Card::VALUE_SIX),
            new Card(Card::COLOR_CLUB, Card::VALUE_SIX),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_SIX),
            new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN),
            new Card(Card::COLOR_HEART, Card::VALUE_SEVEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_SEVEN),
            new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT),
            new Card(Card::COLOR_HEART, Card::VALUE_EIGHT),
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_EIGHT),
            new Card(Card::COLOR_SPADE, Card::VALUE_NINE),
            new Card(Card::COLOR_HEART, Card::VALUE_NINE),
            new Card(Card::COLOR_CLUB, Card::VALUE_NINE),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_NINE),
            new Card(Card::COLOR_SPADE, Card::VALUE_TEN),
            new Card(Card::COLOR_HEART, Card::VALUE_TEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_TEN),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_TEN),
        ]);

        $this->player1 = new Player('Andy');
        $this->player2 = new Player('Tom');
        $this->player3 = new Player('John');

        $this->table = new Table($deck, $playedCard);
        $this->table->addPlayer($this->player1);
        $this->table->addPlayer($this->player2);
        $this->table->addPlayer($this->player3);

        $this->serviceUnderTest = new CardActionService($this->table);
    }

    public function testShouldGiveNextPlayerTwoCardsWhenCardTwoWasDropped(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(2, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveThirdPlayerFourCardsWhenCardTwoWasDroppedAndSecondPlayerHasCardTwoToDefend(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);

        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_TWO));

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(4, $this->player3->getCards());
        $this->assertSame($this->player1, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveFirstPlayerSixCardsWhenCardTwoWasDroppedAndThirdPlayerHasCardTwoToDefend(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);

        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_TWO));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, Card::VALUE_TWO));

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(6, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveSecondPlayerEightCardsWhenCardTwoWasDroppedAndAllPlayersHasCardTwoToDefend(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_TWO);

        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_TWO));
        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_TWO));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, Card::VALUE_TWO));

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(8, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(0, $this->player1->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveNextPlayerThreeCardsWhenCardThreeWasDropped(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(3, $this->player2->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveThirdPlayerSixCardsWhenCardThreeWasDroppedAndSecondPlayerHasCardThreeToDefend(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);

        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_THREE));

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(6, $this->player3->getCards());
        $this->assertSame($this->player1, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveFirstPlayerNineCardsWhenCardThreeWasDroppedAndThirdPlayerHasCardThreeToDefend(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);

        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_THREE));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, Card::VALUE_THREE));

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(9, $this->player1->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldGiveSecondPlayerTwelveCardsWhenCardThreeWasDroppedAndAllPlayersHasCardThreeToDefend(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_THREE);

        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, Card::VALUE_THREE));
        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_THREE));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, Card::VALUE_THREE));

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertCount(12, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertCount(0, $this->player1->getCards());
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldSkipRoundForNextPlayerWhenCardFourWasDropped(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FOUR);

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertSame($this->player3, $this->table->getCurrentPlayer());
    }

    public function testShouldSkipManyRoundsForNextPlayerWhenCardFourWasDroppedAndNextPlayersHaveCardsFourToDefend(): void {
        // Given
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_FOUR);

        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, Card::VALUE_FOUR));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, Card::VALUE_FOUR));

        // When
        $this->serviceUnderTest->afterCard($card);

        // Then
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
        $this->assertEquals(2, $this->player1->getRoundsToSkip());
        $this->assertFalse($this->player1->canPlayRound());
        $this->assertTrue($this->player2->canPlayRound());
        $this->assertTrue($this->player3->canPlayRound());
    }

    public function testShouldRequestCardByValueWhenJackCardWasDroppedAndTakeCardForEachPlayerWhenTheyHaveNotRequestedCard(): void {
        // Given
        $requestValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_JACK);

        $requestedCard = new Card(Card::COLOR_SPADE, $requestValue);
        $this->player1->getCards()->add($requestedCard);

        // When
        $this->serviceUnderTest->afterCard($card, $requestValue);

        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(1, $this->player2->getCards());
        $this->assertCount(1, $this->player3->getCards());
        $this->assertSame($requestedCard, $this->table->getPlayedCards()->getLastCard());
    }

    public function testShouldRequestCardByValueWhenJackCardWasDroppedAndPickCardForEachPlayerWhenTheyHaveRequestedCard(): void {
        // Given
        $requestValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_JACK);

        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, $requestValue));
        $this->player2->getCards()->add(new Card(Card::COLOR_HEART, $requestValue));
        $this->player3->getCards()->add(new Card(Card::COLOR_CLUB, $requestValue));

        // When
        $this->serviceUnderTest->afterCard($card, $requestValue);

        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(0, $this->player2->getCards());
        $this->assertCount(0, $this->player3->getCards());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }

    public function testShouldAllowDroppingManyRequestedCardsByValueWhenPlayerDropsJackCard(): void {
        // Given
        $requestValue = Card::VALUE_SEVEN;
        $card = new Card(Card::COLOR_SPADE, Card::VALUE_JACK);

        $requestedCard = new Card(Card::COLOR_CLUB, $requestValue);
        $this->player1->getCards()->add(new Card(Card::COLOR_SPADE, $requestValue));
        $this->player1->getCards()->add(new Card(Card::COLOR_HEART, $requestValue));
        $this->player1->getCards()->add($requestedCard);

        // When
        $this->serviceUnderTest->afterCard($card, $requestValue);

        // Then
        $this->assertCount(0, $this->player1->getCards());
        $this->assertCount(1, $this->player2->getCards());
        $this->assertCount(1, $this->player3->getCards());
        $this->assertSame($requestedCard, $this->table->getPlayedCards()->getLastCard());
        $this->assertSame($this->player2, $this->table->getCurrentPlayer());
    }
}
