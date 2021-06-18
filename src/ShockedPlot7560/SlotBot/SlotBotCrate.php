<?php

namespace ShockedPlot7560\SlotBot;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class SlotBotCrate {

    private $slotBot;

    public function __construct(Config $slotBot) {
        $this->slotBot = $slotBot;
    }

    /**
     * @return Item[]
     */
    public function getDrops() : array{
        return array_map(
            function (array $itemData) : Item {
                $item = Item::get($itemData["id"], $itemData["meta"], $itemData["amount"]);

                if (isset($itemData["name"])) $item->setCustomName($itemData["name"]);
                if (isset($itemData["lore"])) $item->setLore(explode("\n", $itemData["lore"]));
                if (isset($itemData["enchantments"])) {
                    foreach ($itemData["enchantments"] as $enchantmentData) {
                        if (!isset($enchantmentData["name"]) || !isset($enchantmentData["level"])) {
                            SlotBot::getInstance()->getLogger()->error("Invalid enchantment configuration used in SlotBotCrate");
                            continue;
                        }
                        $enchantment = Enchantment::getEnchantmentByName($enchantmentData["name"]);
                        if ($enchantment !== null) $item->addEnchantment(new EnchantmentInstance($enchantment, $enchantmentData["level"]));
                    }
                }
                return $item;
            }, 
            $this->slotBot->get("crate")['drops'] ?? []
        );
    }

    public function getDrop():Item{
        return $this->getDrops()[array_rand($this->getDrops())];
    }
}