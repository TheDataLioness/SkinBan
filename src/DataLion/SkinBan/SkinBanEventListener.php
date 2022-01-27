<?php

namespace DataLion\SkinBan;

use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;

class SkinBanEventListener implements Listener
{

    public function __construct(private SkinBanPlugin $skinBanPlugin)
    {}

    public function onJoin(PlayerJoinEvent $event): void
    {
        $this->banCheck($event->getPlayer());
    }

    public function skinChange(PlayerChangeSkinEvent $event): void
    {
        if($this->banCheck($event->getPlayer())){
            $event->cancel();
        }
    }

    private function banCheck(Player $player): bool
    {
        $skinBan = SkinBanPlugin::getDataprovider()->getBanByUsername($player->getName());
        if(is_null($skinBan)) return false;
        if($skinBan->getBannedUntil() < time())
        {
            SkinBanPlugin::getDataprovider()->removeBan($skinBan);
            return false;
        }

        $boringSkin = new Skin("Standard_Custom", str_repeat(random_bytes(3) . "\xff", 4096));
        $player->setSkin($boringSkin);
        $player->sendSkin();

        $dateFormat = $this->skinBanPlugin->getConfig()->get("dateformat", 'd-m-Y H:i:s');
        if($skinBan->getBannedUntil() !== -1){
            $dateUnban = gmdate($dateFormat, $skinBan->getBannedUntil());
        }else{
            $dateUnban = 'never';
        }

        $message = $this->skinBanPlugin->getConfig()->get("skinbanned_message", "§4[§cSkinBan§4] §cYour skin has been banned. You won't be able to use your skin until {date}.");
        $message = str_replace("{player}", $player->getName(), str_replace("{date}", $dateUnban, $message));
        $player->sendMessage($message);
        return true;
    }
}