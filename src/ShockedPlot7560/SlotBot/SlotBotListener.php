<?php

namespace ShockedPlot7560\SlotBot;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use ShockedPlot7560\SlotBot\Item\KeyItem;

class SlotBotListener implements Listener {

    /** @var \ShockedPlot7560\SlotBot\SlotBot */
    private $slotBot;

    public function __construct(SlotBot $slotBot)
    {
        $this->slotBot = $slotBot;
    }

    public function onQuit(PlayerQuitEvent $event) {

        $player = $event->getPlayer();
        $count = $this->slotBot::getTicket($player->getName());
        if ($count !== null) {
            $player->getInventory()->addItem(KeyItem::get($count));
            $this->slotBot::removeTicket($player->getName());
        }

    }

}