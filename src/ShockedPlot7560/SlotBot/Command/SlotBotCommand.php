<?php

namespace ShockedPlot7560\SlotBot\Command;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use ShockedPlot7560\SlotBot\Item\KeyItem;
use ShockedPlot7560\SlotBot\Menu\SlotBotMenu;
use ShockedPlot7560\SlotBot\SlotBot;

class SlotBotCommand extends PluginCommand{

    public $Main;

    public function __construct(SlotBot $Main)
    {
        parent::__construct("slotbot", $Main);
        $this->setDescription("");
        $this->setUsage("/slotbot [give] <string: player> <int: count>");
        $this->setAliases(['sb']);
        $this->Main = $Main;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (count($args) > 0 && $args[0] === "give") {
            if ($args[0] !== "give") {
                $sender->sendMessage("§4Usage: " . $this->getUsage());
                return;
            }
            if (!isset($args[2])) {
                $sender->sendMessage("§4Usage: " . $this->getUsage());
                return;
            }
            $target = SlotBot::getInstance()->getServer()->getPlayer($args[1]);
            if (!$target instanceof Player) {
                $sender->sendMessage("§4Player not found");
                return;
            }
            $target->getInventory()->addItem(KeyItem::get((int) $args[2]));
        }else{
            if (!$sender instanceof Player) {
                return;
            }
            new SlotBotMenu($sender->getPlayer());
        }
    }
}