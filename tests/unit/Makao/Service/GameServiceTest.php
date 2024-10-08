<?php

namespace Tests\unit\Makao\Service;

use Makao\Card;
use Makao\Collection\CardCollection;
use Makao\Exception\CardNotFoundException;
use Makao\Exception\GameException;
use Makao\Player;
use Makao\Service\CardActionService;
use Makao\Service\CardSelector\CardSelectorInterface;
use Makao\Service\CardService;
use Makao\Service\GameService;
use Makao\Table;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase {
    private readonly GameService $gameServiceUnderTest;
    private readonly MockObject|CardService $cardServiceMock;
    private readonly MockObject|CardActionService $cardActionServiceMock;
    private readonly MockObject|CardSelectorInterface $cardSelectorMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void {
        $this->cardSelectorMock = $this->createMock(CardSelectorInterface::class);
        $this->cardActionServiceMock = $this->createMock(CardActionService::class);
        $this->cardServiceMock = $this->createMock(CardService::class);

        $this->gameServiceUnderTest = new GameService(
            new Table(),
            $this->cardServiceMock,
            $this->cardSelectorMock,
            $this->cardActionServiceMock,
        );
    }

    public function testShouldReturnFalseWhenGameIsNotStarted(): void {
        // When
        $actual = $this->gameServiceUnderTest->isStarted();

        // Then
        $this->assertFalse($actual);
    }

    public function testShouldReturnTrueWhenGameIsNotStarted(): void {
        // Given
        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_TWO),
            $noActionCard,
            new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_TEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_NINE),
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_SIX),
            new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
        ]);

        $this->gameServiceUnderTest->getTable()->addCardCollectionToDeck($collection);

        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willReturn($noActionCard);

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

    public function testShouldChooseNoActionCardAsFirstPlayedCardWhenStartingGame(): void {
        // Given
        $table = $this->gameServiceUnderTest->getTable();
        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);

        $this->gameServiceUnderTest->addPlayers([
            new Player('John'),
            new Player('Andy'),
        ]);

        $collection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_TWO),
            $noActionCard,
            new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_TEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_NINE),
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_SIX),
            new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
        ]);

        $table->addCardCollectionToDeck($collection);

        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willReturn($noActionCard);

        // When
        $this->gameServiceUnderTest->startGame();

        // Then
        $this->assertCount(1, $table->getPlayedCards());
        $this->assertSame($noActionCard, $table->getPlayedCards()->pickCard());
    }

    public function testShouldThrowGameExceptionWhenCardServiceThrowsException(): void {
        // Expect
        $notFoundException = new CardNotFoundException('No regular cards in collection');
        $gameException = new GameException('The game needs help!', $notFoundException);

        $this->expectExceptionObject($gameException);
        $this->expectExceptionMessage('The game needs help! Issue: No regular cards in collection');

        // Given
        $collection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_ACE),
            new Card(Card::COLOR_SPADE, Card::VALUE_FOUR),
        ]);

        $table = $this->gameServiceUnderTest->getTable();
        $table->addCardCollectionToDeck($collection);

        $this->gameServiceUnderTest->addPlayers([
            new Player('John'),
            new Player('Andy'),
        ]);

        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willThrowException($notFoundException);

        // When
        $this->gameServiceUnderTest->startGame();
    }

    public function testShouldPlayersTakeFiveCardsFromDeckOnStartGame(): void {
        // Given
        $players = [
            new Player('John'),
            new Player('Andy'),
            new Player('Tom'),
        ];

        $this->gameServiceUnderTest->addPlayers($players);

        $table = $this->gameServiceUnderTest->getTable();

        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_HEART, Card::VALUE_TWO),
            $noActionCard,
            new Card(Card::COLOR_HEART, Card::VALUE_FIVE),
            new Card(Card::COLOR_DIAMOND, Card::VALUE_FIVE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
            new Card(Card::COLOR_CLUB, Card::VALUE_TEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_NINE),
            new Card(Card::COLOR_CLUB, Card::VALUE_EIGHT),
            new Card(Card::COLOR_CLUB, Card::VALUE_SEVEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_SIX),
            new Card(Card::COLOR_CLUB, Card::VALUE_FIVE),
            new Card(Card::COLOR_CLUB, Card::VALUE_FOUR),
            new Card(Card::COLOR_CLUB, Card::VALUE_THREE),
            new Card(Card::COLOR_CLUB, Card::VALUE_TWO),
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
        ]);

        $table->addCardCollectionToDeck($collection);

        $this->cardServiceMock->expects($this->once())
            ->method('pickFirstNoActionCard')
            ->with($collection)
            ->willReturn($noActionCard);

        // When
        $this->gameServiceUnderTest->startGame();

        // Then
        foreach($players as $player) {
            $this->assertCount(5, $player->getCards());
        }
    }

    public function testShouldChooseCardToPlayFromPlayersCardsAndPutItOnTheTable(): void {
        // Given
        $correctCard = new Card(Card::COLOR_HEART, Card::VALUE_FIVE);

        $player1 = new Player('John', new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT),
            $correctCard,
        ]));

        $player2 = new Player('Andy');

        $this->gameServiceUnderTest->addPlayers([$player1, $player2]);

        $table = $this->gameServiceUnderTest->getTable();
        $playedCard = new Card(Card::COLOR_HEART, Card::VALUE_SIX);
        $table->addPlayedCard($playedCard);

        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
        ]);

        $table->addCardCollectionToDeck($collection);

        $this->cardSelectorMock->expects($this->once())
            ->method('chooseCard')
            ->with($player1, $playedCard, $table->getPlayedCardColor())
            ->willReturn($correctCard);

        $this->cardActionServiceMock->expects($this->once())
            ->method('afterCard')
            ->with($correctCard);

        // When
        $this->gameServiceUnderTest->playRound();

        // Then
        $this->assertSame($correctCard, $table->getPlayedCards()->getLastCard());
    }

    public function testShouldGivePlayerOneCardWhenHeHasNoCorrectCardToPlay(): void {
        // Given
        $player1 = new Player('John', new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT),
            new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN),
        ]));

        $player2 = new Player('Andy');

        $this->gameServiceUnderTest->addPlayers([$player1, $player2]);

        $table = $this->gameServiceUnderTest->getTable();
        $playedCard = new Card(Card::COLOR_HEART, Card::VALUE_SIX);
        $table->addPlayedCard($playedCard);

        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
        ]);

        $table->addCardCollectionToDeck($collection);

        $this->cardSelectorMock->expects($this->once())
            ->method('chooseCard')
            ->with($player1, $playedCard, $table->getPlayedCardColor())
            ->willThrowException(new CardNotFoundException());

        $this->cardActionServiceMock->expects($this->never())
            ->method('afterCard');

        // When
        $this->gameServiceUnderTest->playRound();

        // Then
        $this->assertSame($playedCard, $table->getPlayedCards()->getLastCard());
        $this->assertCount(3, $player1->getCards());
        $this->assertCount(3, $table->getCardDeck());
        $this->assertSame($player2, $table->getCurrentPlayer());
    }

    public function testShouldSkipPlayerRoundWhenHeCannotPlayRound(): void {
        // Given
        $player1 = new Player('John', new CardCollection([
            new Card(Card::COLOR_SPADE, Card::VALUE_EIGHT),
            new Card(Card::COLOR_SPADE, Card::VALUE_SEVEN),
        ]));

        $player2 = new Player('Andy');

        $this->gameServiceUnderTest->addPlayers([$player1, $player2]);

        $table = $this->gameServiceUnderTest->getTable();
        $playedCard = new Card(Card::COLOR_HEART, Card::VALUE_SIX);
        $table->addPlayedCard($playedCard);

        $noActionCard = new Card(Card::COLOR_CLUB, Card::VALUE_FIVE);
        $collection = new CardCollection([
            new Card(Card::COLOR_CLUB, Card::VALUE_ACE),
            new Card(Card::COLOR_CLUB, Card::VALUE_KING),
            new Card(Card::COLOR_CLUB, Card::VALUE_QUEEN),
            new Card(Card::COLOR_CLUB, Card::VALUE_JACK),
        ]);

        $table->addCardCollectionToDeck($collection);

        $this->cardSelectorMock->expects($this->never())
            ->method('chooseCard')
            ->with($player1, $playedCard, $table->getPlayedCardColor());

        $this->cardActionServiceMock->expects($this->never())
            ->method('afterCard');

        $player1->addRoundToSkip(2);

        // When
        $this->gameServiceUnderTest->playRound();

        // Then
        $this->assertSame($playedCard, $table->getPlayedCards()->getLastCard());
        $this->assertCount(2, $player1->getCards());
        $this->assertCount(4, $table->getCardDeck());
        $this->assertSame($player2, $table->getCurrentPlayer());
    }
}
