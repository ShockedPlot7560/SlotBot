<?php

namespace ShockedPlot7560\SlotBot;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use ShockedPlot7560\SlotBot\Command\SlotBotCommand;

class SlotBot extends PluginBase {

    private static $instance;
    private static $crate;

    private static $current;

    public function onEnable()
    {
        self::$instance = $this;
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }

        $this->getServer()->getPluginManager()->registerEvents(new SlotBotListener($this), $this);
        $this->getServer()->getCommandMap()->register("slotbot", new SlotBotCommand($this));

        $this->saveResource("crates.yml");
        $this->saveDefaultConfig();

        self::$crate = new SlotBotCrate(new Config($this->getDataFolder() . "crates.yml"));
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public static function getCrate(): SlotBotCrate{
        return self::$crate;
    }

    public static function getTicket(string $playerName) : ?int{
        return self::$current[$playerName] ?? null;
    }

    public static function setTicket(string $playerName, int $count){
        self::$current[$playerName] = $count;
    }

    public static function removeTicket(string $playerName){
        if (isset(self::$current[$playerName])) unset(self::$current[$playerName]);
    }
}