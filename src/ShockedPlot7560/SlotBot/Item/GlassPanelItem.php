<?php

namespace ShockedPlot7560\SlotBot\Item;

use pocketmine\item\Item;

class GlassPanelItem {

    public static function get(int $meta, int $count = 1) : Item {
        $item = Item::get(Item::STAINED_GLASS_PANE, $meta, $count);
        $item->setCustomName(" ");
        return $item;
    }

}