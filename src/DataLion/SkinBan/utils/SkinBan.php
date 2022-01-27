<?php

namespace DataLion\SkinBan\utils;

class SkinBan
{

    public function __construct(private $username, private $bannedUntil)
    {}

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getBannedUntil(): int
    {
        return $this->bannedUntil;
    }

}