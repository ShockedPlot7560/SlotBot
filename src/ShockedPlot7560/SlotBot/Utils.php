<?php

namespace ShockedPlot7560\SlotBot;

use pocketmine\block\StainedGlassPane;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\item\ItemFactory;
use ShockedPlot7560\SlotBot\Item\GlassPanelItem;
use ShockedPlot7560\SlotBot\Item\KeyItem;
use ShockedPlot7560\SlotBot\Item\RestartItem;
use ShockedPlot7560\SlotBot\Item\StartItem;

class Utils {

    public static function getNumberTicket(Inventory $inventory):int{
        $slot = self::getSlotPosition(0, 1);
        $counter = 0;
        for ($i = $slot; $i < $slot + 5; $i++) { 
            $Item = $inventory->getItem($i);
            if (self::isValidKey($Item)) {
                $counter++;
            }
        }
        return $counter;
    }

    public static function getPositionFromSlot(int $slot) {
        $ticket = ($slot % 9) - 2;
        $slotIndex = ($slot - 2 - $ticket)/9;
        return [$ticket, $slotIndex];
    }

    public static function getSlotPosition(int $ticket, int $slotIndex) : int{
        return 2 + $ticket + (9 * $slotIndex);
    }

    public static function setColumnReady(Inventory $inventory, int $index, bool $static = false) {
        $position = Utils::getPositionFromSlot($index);
        for ($i=1; $i < 5; $i++) { 
            $slot = Utils::getSlotPosition($position[0], $position[1] + $i);
            $inventory->setItem($slot, GlassPanelItem::get($static ? 0 : 5));
        }
        if ($position[0] < 4) {
            $slot = Utils::getSlotPosition($position[0] + 1, $position[1]);
            $inventory->setItem($slot, $static ? GlassPanelItem::get(0) : Item::get(Item::AIR));
        }
    }

    public static function setGamesStart(Inventory $inventory, int $ticket) {
        $slot = self::getSlotPosition($ticket, 1);
        if ($ticket < 5) $inventory->setItem($slot, GlassPanelItem::get(0));
        for ($i=self::getSlotPosition($ticket, 3); $i < self::getSlotPosition(5, 3); $i++) { 
            $inventory->setItem($i, GlassPanelItem::get(14));
        }
    }

    public static function getReward(Inventory $inventory):array{
        $array = [];
        for ($i=self::getSlotPosition(0, 3); $i < self::getSlotPosition(5, 3); $i++) {
            $item = $inventory->getItem($i);
            if (!self::isPanel($item)) {
                $array[] = $item;
            }
        }
        return $array;
    }

    public static function setGamesFinish(Inventory $inventory) {
        $column = [1,2,4,5];
        foreach ($column as $col) {
            for ($i=self::getSlotPosition(0, $col); $i < self::getSlotPosition(5, $col); $i++) {
                $inventory->setItem($i, GlassPanelItem::get(0));
            }
        }
        $inventory->setItem(35, RestartItem::get());
    }

    public static function setGamesInit(Inventory $inventory) {
        $map = [
            [7,15,7,7,7,7,7,15,7],
            [7,15,null,0,0,0,0,15,7],
            [7,15,0,0,0,0,0,15,7],
            [7,15,0,0,0,0,0,15,7],
            [7,15,0,0,0,0,0,15,7],
            [7,15,0,0,0,0,0,15,7]
        ];
        $i = 0;
        $Items = [];
        foreach ($map as $key => $var) {
            foreach ($var as $key => $id) {
                if ($id !== null) {
                    $Items[$i] = GlassPanelItem::get($id);
                }
                $i++;
            }
        }
        $papCount= 1;
        for ($i=2; $i < 7; $i++) { 
            $Items[$i] = GlassPanelItem::get(3, $papCount);
            $papCount++;
        }
        $Items[35] = StartItem::get();
        $Items[27] = GlassPanelItem::get(14);
        $inventory->setContents($Items);
    }

    public static function canRemoveTicket(Inventory $inventory, int $slot) : bool {
        if ($inventory->getItem($slot+1)->getId() === Item::STAINED_GLASS_PANE ||
            $inventory->getItem($slot+1)->getId() === Item::AIR) {
            return true;
        }else{
            return false;
        }
    }

    public static function isPanel($item):bool{
        if ($item instanceof ItemBlock && $item->getBlock() instanceof StainedGlassPane) {
            return true;
        }
        return false;
    }

    public static function isValidKey(Item $item) {
        $comparaison = KeyItem::get(1);
        return $item->getId() === $comparaison->getId() &&
            $item->getDamage() === $comparaison->getDamage() &&
            $item->getCustomName() === $comparaison->getCustomName() &&
            $item->getLore() === $comparaison->getLore();
    }

    public static function isValidStart(Item $item) {
        $comparaison = StartItem::get();
        return $item->getId() === $comparaison->getId() &&
            $item->getDamage() === $comparaison->getDamage() &&
            $item->getCustomName() === $comparaison->getCustomName() &&
            $item->getLore() === $comparaison->getLore();
    }

    public static function isValidRestart(Item $item) {
        $comparaison = RestartItem::get();
        return $item->getId() === $comparaison->getId() &&
            $item->getDamage() === $comparaison->getDamage() &&
            $item->getCustomName() === $comparaison->getCustomName() &&
            $item->getLore() === $comparaison->getLore();
    }
}