<?php

namespace DataLion\SkinBan\commands;

use DataLion\SkinBan\SkinBanPlugin;
use DataLion\SkinBan\utils\SkinBan;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\entity\Skin;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;

class SkinbanCommand extends Command implements PluginOwned
{

    private Plugin $plugin;

    public function __construct(Plugin $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->plugin = $plugin;
        $this->setPermission("skinban.command.skinban");
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

        $days = $args[1] ?? null;
        $hours = $args[2] ?? null;
        $minutes = $args[3] ?? null;

        if ($days === null || $hours === null || $minutes === null) {
            $banTime = -1;
        }else{
            if(!is_numeric($days) || !is_numeric($hours) || !is_numeric($minutes)){
                $sender->sendMessage("§4[§cSkinBan§4] §cPlease enter valid arguments.");
                return;
            }

            $banTime = time();
            $banTime += intval($days) * 86400;
            $banTime += intval($hours) * 3600;
            $banTime += intval($minutes) * 60;
        }

        $dataProvider = SkinBanPlugin::getDataprovider();
        $skinBan = $dataProvider->getBanByUsername($username);
        if(!is_null($skinBan)){
            $sender->sendMessage("§4[§cSkinBan§4] §cThis player is already skinbanned!");
            return;
        }

        $skinBan = new SkinBan($username, $banTime);
        $dataProvider->submitBan($skinBan);
        $sender->sendMessage("§4[§cSkinBan§4] §cYou have successfully skinbanned §6{$username}§c!");

        $player = Server::getInstance()->getPlayerExact($username);
        if(!is_null($player) && $player->isOnline()){
            $boringSkin = new Skin("Standard_Custom", str_repeat(random_bytes(3) . "\xff", 4096));
            $player->setSkin($boringSkin);
            $player->sendSkin();
        }

    }

}