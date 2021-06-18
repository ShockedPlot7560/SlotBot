<?php

namespace ShockedPlot7560\SlotBot\Item;

use pocketmine\item\Item;
use ShockedPlot7560\SlotBot\SlotBot;
use pocketmine\nbt\tag\ListTag;

class KeyItem {

    public static function get(int $amount) : Item {
        $key = Item::get((int)SlotBot::getInstance()->getConfig()->getNested("keys.id"), (int)SlotBot::getInstance()->getConfig()->getNested("keys.meta"), $amount);
        $key->setCustomName(SlotBot::getInstance()->getConfig()->getNested("keys.name"));
        $key->setLore([SlotBot::getInstance()->getConfig()->getNested("keys.lore")]);
        $key->setNamedTagEntry(new ListTag(Item::TAG_ENCH));
        return $key;
    }

}