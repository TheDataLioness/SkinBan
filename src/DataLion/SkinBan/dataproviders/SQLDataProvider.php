<?php

namespace DataLion\SkinBan\dataproviders;

use DataLion\SkinBan\utils\SkinBan;

class SQLDataProvider implements DataProvider
{

    public function __construct(private \SQLite3 $db)
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS skinbans(
            username VARCHAR NOT NULL,
            until_timestamp INTEGER NOT NULL
        )");
    }

    public function submitBan(SkinBan $skinBan): void
    {
        $stmt = $this->db->prepare("INSERT INTO skinbans(username, until_timestamp) VALUES(:username, :until_timestamp)");
        $stmt->bindValue(":username", $skinBan->getUsername());
        $stmt->bindValue(":until_timestamp", $skinBan->getBannedUntil());
        $stmt->execute();
        $stmt->close();
    }

    public function removeBan(SkinBan $skinBan): void
    {
        $stmt = $this->db->prepare("DELETE FROM skinbans WHERE username = :username");
        $stmt->bindValue(":username", $skinBan->getUsername());
        $stmt->execute();
        $stmt->close();
    }

    public function getBanByUsername(string $username): ?SkinBan
    {
        $stmt = $this->db->prepare("SELECT * FROM skinbans WHERE username = :username");
        $stmt->bindValue(":username", $username);
        $result = $stmt->execute();
        $value = $result->fetchArray(SQLITE3_ASSOC);
        $stmt->close();
        return $value !== false && sizeof($value) > 0 ? new SkinBan($value["username"], $value["until_timestamp"]) : null;
    }

    /**
     * @return SkinBan[]
     */
    public function getBans(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM skinbans");
        $result = $stmt->execute();
        $bans = [];
        while ($value = $result->fetchArray(SQLITE3_ASSOC)) {
            $bans[] = new SkinBan($value["username"], $value["until_timestamp"]);
        }
        $stmt->close();
        return $bans;
    }
}