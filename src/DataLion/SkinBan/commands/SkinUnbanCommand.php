<?php

namespace DataLion\SkinBan\commands;

use DataLion\SkinBan\SkinBanPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class SkinUnbanCommand extends Command implements PluginOwned
{

    private Plugin $plugin;

    public function __construct(Plugin $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->plugin = $plugin;
        $this->setPermission("skinban.command.skinunban");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $username = $args[0] ?? null;
        if ($username === null) {
            $sender->sendMessage($this->getUsage());
            return;
        }

        $dataProvider = SkinBanPlugin::getDataprovider();
        $skinBan = $dataProvider->getBanByUsername($username);
        if(is_null($skinBan)){
            $sender->sendMessage("§4[§cSkinBan§4] §cThis player is not skinbanned!");
            return;
        }
        $dataProvider->removeBan($skinBan);
        $sender->sendMessage("§4[§cSkinBan§4] §cYou have successfully removed the skinban of §6{$username}§c!");
    }

}