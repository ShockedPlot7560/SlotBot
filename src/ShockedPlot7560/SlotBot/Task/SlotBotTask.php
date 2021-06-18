<?php

namespace ShockedPlot7560\SlotBot\Task;

use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use ShockedPlot7560\SlotBot\SlotBot;
use ShockedPlot7560\SlotBot\Utils;

class SlotBotTask extends Task {

    /** @var Player */
    private $player;
    /** @var \muqsit\invmenu\InvMenu */
    private $menu;
    /** @var int */
    private $currentTick = 0;
    /** @var int */
    private $ticketCount;

    /** @var Item[] */
    private $lastRewards = [];

    public function __construct(Player $player, InvMenu $menu, int $ticketCount){
        $this->player = $player;
        $this->ticketCount = $ticketCount;

        $this->menu = $menu;
    }

    public function onRun(int $currentTick){
        $speed = 1;
        $duration = 120;
        $safeDuration = (($duration / $speed) >= 5.5) ? $duration : (5.5 * $speed);
        if ($this->currentTick >= $safeDuration) {
            $this->finish();
            return;
        }
        if ($this->currentTick % $speed === 0) {
            for ($ticketCrawl=0; $ticketCrawl < $this->ticketCount; $ticketCrawl++) { 
                $this->lastRewards[Utils::getSlotPosition($ticketCrawl, 0)] = SlotBot::getCrate()->getDrop();
            }
            foreach ($this->lastRewards as $slot => $lastReward) {
                $position = Utils::getPositionFromSlot($slot);
                $nextSlot = Utils::getSlotPosition($position[0], $position[1] + 1);
                $this->lastRewards[$nextSlot] = $lastReward;
                $this->player->getLevel()->broadcastLevelSoundEvent($this->player, LevelSoundEventPacket::SOUND_NOTE);
                if ($nextSlot < 55) $this->menu->getInventory()->setItem($nextSlot, $lastReward);
            }
            if ($this->currentTick >= 10) {
                $speed += 0.1;
                $safeDuration = (($duration / $speed) >= 5.5) ? $duration : (5.5 * $speed);
            }
        }
        $this->currentTick++;
    }

    private function finish() {
        Utils::setGamesFinish($this->menu->getInventory());
        SlotBot::getInstance()->getScheduler()->cancelTask($this->getTaskId());
        foreach (Utils::getReward($this->menu->getInventory()) as $rewardItem) {
            if ($this->player->getInventory()->canAddItem($rewardItem)) {
                $this->player->getInventory()->addItem($rewardItem);
            }else{
                $this->player->dropItem($rewardItem);
            }
        }
        SlotBot::removeTicket($this->player->getName());
        $this->player->getLevel()->broadcastLevelSoundEvent($this->player, LevelSoundEventPacket::SOUND_LEVELUP);
    }
}