<?php

namespace ShockedPlot7560\SlotBot\Item;

use pocketmine\item\Item;
use ShockedPlot7560\SlotBot\SlotBot;

class RestartItem {

    public static function get() : Item {
        $item = Item::get(SlotBot::getInstance()->getConfig()->getNested("restart.id"), SlotBot::getInstance()->getConfig()->getNested("restart.meta"));
        $item->setCustomName(SlotBot::getInstance()->getConfig()->getNested("restart.name"));
        $item->setLore(\explode("\n", SlotBot::getInstance()->getConfig()->getNested("restart.lore")));
        return $item;
    }

}