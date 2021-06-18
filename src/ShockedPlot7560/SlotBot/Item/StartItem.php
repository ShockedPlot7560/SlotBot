<?php

namespace ShockedPlot7560\SlotBot\Item;

use pocketmine\item\Item;
use ShockedPlot7560\SlotBot\SlotBot;

class StartItem {

    public static function get() : Item {
        $item = Item::get(SlotBot::getInstance()->getConfig()->getNested("start.id"), SlotBot::getInstance()->getConfig()->getNested("start.meta"));
        $item->setCustomName(SlotBot::getInstance()->getConfig()->getNested("start.name"));
        $item->setLore(\explode("\n", SlotBot::getInstance()->getConfig()->getNested("start.lore")));
        return $item;
    }

}