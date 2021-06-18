<?php

namespace ShockedPlot7560\SlotBot\Menu;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use ShockedPlot7560\SlotBot\Item\GlassPanelItem;
use ShockedPlot7560\SlotBot\Item\KeyItem;
use ShockedPlot7560\SlotBot\Item\RestartItem;
use ShockedPlot7560\SlotBot\Item\StartItem;
use ShockedPlot7560\SlotBot\SlotBot;
use ShockedPlot7560\SlotBot\Task\SlotBotTask;
use ShockedPlot7560\SlotBot\Utils;

class SlotBotMenu {

    protected $inventoryType = InvMenu::TYPE_DOUBLE_CHEST;
    protected $player;
    private $menu;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->menu = InvMenu::create($this->inventoryType);
        Utils::setGamesInit($this->menu->getInventory());
        $this->setListeners();
        $this->menu->send($player);
        $this->player->getLevel()->broadcastLevelSoundEvent($this->player, LevelSoundEventPacket::SOUND_ENDERCHEST_OPEN);
    }

    public function throw(Player $player) {
        $this->menu->getInventory()->setItem(35, GlassPanelItem::get(14));
        $ticket = Utils::getNumberTicket($this->menu->getInventory());
        SlotBot::setTicket($player->getName(), $ticket);
        Utils::setGamesStart($this->menu->getInventory(), $ticket);
        SlotBot::getInstance()->getScheduler()->scheduleRepeatingTask(new SlotBotTask($player, $this->menu, $ticket), 1);
    }

    public function getMenu() : InvMenu {
        return $this->menu;
    }

    public function setListeners() {
        $this->menu->setListener(
            function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
                $inventories = $transaction->getTransaction()->getInventories();
                if (count($inventories) > 1) {
                    if ($inventories[\array_key_first($inventories)] instanceof PlayerInventory) {
                        if (Utils::isValidKey($transaction->getIn()) &&
                            $transaction->getOut()->getId() === Item::AIR && 
                            $transaction->getIn()->getCount() === 1) {
                            $result = $transaction->continue();
                            Utils::setColumnReady($this->menu->getInventory(), $transaction->getAction()->getSlot());
                        }else{
                            $result = $transaction->discard();
                        }
                    }else{
                        if (Utils::isValidKey($transaction->getOut()) && 
                            $transaction->getIn()->getId() === Item::AIR && 
                            $transaction->getOut()->getCount() === 1 && 
                            Utils::canRemoveTicket($inventories[\array_key_first($inventories)], $transaction->getAction()->getSlot())) {
                            $result = $transaction->continue();
                            Utils::setColumnReady($this->menu->getInventory(), $transaction->getAction()->getSlot(), true);
                        }else{
                            $result = $transaction->discard();
                        }
                    }
                }else{
                    if (Utils::isValidKey($transaction->getIn()) &&
                        $transaction->getOut()->getId() === Item::AIR && 
                        $transaction->getIn()->getCount() === 1) {
                            $result = $transaction->continue();
                    }else{
                        $result = $transaction->discard();
                    }
                } 
                if (Utils::isValidStart($transaction->getOut())) {
                    if (Utils::getNumberTicket($this->menu->getInventory()) > 0) {
                        $this->throw($transaction->getPlayer());
                    }
                }elseif (Utils::isValidRestart($transaction->getOut())) {
                    Utils::setGamesInit($this->menu->getInventory());
                }
                return $result;
		    }
        );
        $this->menu->setInventoryCloseListener(
            function(Player $player, Inventory $inventory) {
                $count = Utils::getNumberTicket($inventory);
                $player->getInventory()->addItem(KeyItem::get($count));
                SlotBot::removeTicket($player->getName());
            }
        );
    }

    public function handle(Player $player, Item $itemClicked, Inventory $inventory, int $slot){
        $start = StartItem::get();
        if ($itemClicked instanceof $start) {
            $this->throw($player);
            return true;
        }
        return true;
    }
}