<?php

namespace DataLion\SkinBan\commands;

use DataLion\SkinBan\SkinBanPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class SkinBanListCommand extends Command implements PluginOwned
{

    private Plugin $plugin;

    public function __construct(Plugin $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->plugin = $plugin;
        $this->setPermission("skinban.command.skinbanlist");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        $ownedPlugin = $this->getOwningPlugin();
        $dateFormat = 'd-m-Y H:i:s';
        if($ownedPlugin instanceof SkinBanPlugin){
            $dateFormat = $ownedPlugin->getConfig()->get("dateformat", $dateFormat);
        }

        $bans = SkinBanPlugin::getDataprovider()->getBans();
        $sender->sendMessage("§4[§cSkinBan§4] §r§7SkinBan List");
        foreach ($bans as $ban) {
            if($ban->getBannedUntil() === -1){
                $dateunban = gmdate($dateFormat, $ban->getBannedUntil());
            }else{
                $dateunban = "never";
            }

            $sender->sendMessage("§8- §7".$ban->getUsername()." §8| §7". $dateunban);
        }
        return true;
    }

}