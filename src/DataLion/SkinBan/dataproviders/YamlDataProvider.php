<?php

namespace DataLion\SkinBan\dataproviders;

use DataLion\SkinBan\utils\SkinBan;
use pocketmine\utils\Config;

class YamlDataProvider implements DataProvider
{

    public function __construct(private Config $skinBanDataConfig)
    {}

    public function submitBan(SkinBan $skinBan): void
    {
        $this->skinBanDataConfig->set($skinBan->getUsername(), $skinBan->getBannedUntil());
        $this->skinBanDataConfig->save();
    }

    public function removeBan(SkinBan $skinBan): void
    {
        $this->skinBanDataConfig->remove($skinBan->getUsername());
        $this->skinBanDataConfig->save();
    }

    public function getBanByUsername(string $username): ?SkinBan
    {
        $bannedUntil = $this->skinBanDataConfig->get($username, null);
        if ($bannedUntil === null) {
            return null;
        }
        return new SkinBan($username, $bannedUntil);
    }

    /**
     * @return SkinBan[]
     */
    public function getBans(): array
    {
        $bans = [];
        foreach ($this->skinBanDataConfig->getAll() as $username => $bannedUntil) {
            $bans[] = new SkinBan($username, $bannedUntil);
        }
        return $bans;
    }
}