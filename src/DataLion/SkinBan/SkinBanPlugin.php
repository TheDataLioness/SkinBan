<?php

declare(strict_types=1);

namespace DataLion\SkinBan;

use DataLion\SkinBan\commands\SkinbanCommand;
use DataLion\SkinBan\commands\SkinBanListCommand;
use DataLion\SkinBan\commands\SkinUnbanCommand;
use DataLion\SkinBan\dataproviders\DataProvider;
use DataLion\SkinBan\dataproviders\SQLDataProvider;
use DataLion\SkinBan\dataproviders\YamlDataProvider;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class SkinBanPlugin extends PluginBase{

    private static DataProvider $dataProvider;

    public static function getDataprovider(): DataProvider
    {
        return self::$dataProvider;
    }

    protected function onLoad(): void
    {
        $this->saveDefaultConfig();
    }

    protected function onEnable(): void
    {
        $this->setupDataprovider();
        $this->getServer()->getPluginManager()->registerEvents(new SkinBanEventListener($this), $this);
        $this->registerCommands();
    }

    private function setupDataprovider(): void
    {
        $dataprovider = null;
        switch ($this->getConfig()->get("dataprovider")){
            case "yaml":
                $skinBanDataConfig = new Config($this->getDataFolder() . "skinbanned.yml", Config::YAML);
                $dataprovider = new YamlDataProvider($skinBanDataConfig);
                break;
            case "sqlite":
                $sqlite = new \SQLite3($this->getDataFolder() . "skinban.sqlite");
                $dataprovider = new SQLDataProvider($sqlite);
                break;
        }

        if(is_null($dataprovider)){
            $this->getLogger()->error("Invalid database provider in config.yml");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        self::$dataProvider = $dataprovider;
    }

    private function registerCommands(): void
    {
        $this->getServer()->getCommandMap()->register($this->getName(), new SkinBanCommand($this, "skinban", "Skinban a player", "/skinban <username> [days] [hours] [minutes]"));
        $this->getServer()->getCommandMap()->register($this->getName(), new SkinUnbanCommand($this, "skinunban", "Unskinban a player", "/skinunban <username>"));
        $this->getServer()->getCommandMap()->register($this->getName(), new SkinBanListCommand($this, "skinbanlist", "List all skinbanned players", "/skinbanlist"));
    }

}
