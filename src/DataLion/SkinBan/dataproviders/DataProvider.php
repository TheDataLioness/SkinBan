<?php

namespace DataLion\SkinBan\dataproviders;

use DataLion\SkinBan\utils\SkinBan;

interface DataProvider
{

    public function submitBan(SkinBan $skinBan): void;
    public function removeBan(SkinBan $skinBan): void;
    public function getBanByUsername(string $username): ?SkinBan;

    /**
     * @return SkinBan[]
     */
    public function getBans(): array;

}