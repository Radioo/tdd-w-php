<?php

namespace Makao\Service\CardSelector;

use Makao\Card;
use Makao\Player;

interface CardSelectorInterface {
    public function chooseCard(Player $player, Card $playedCard, string $acceptColor): Card;
}
