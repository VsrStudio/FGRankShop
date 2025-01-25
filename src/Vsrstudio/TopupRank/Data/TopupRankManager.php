<?php

namespace Vsrstudio\TopupRank\Data;

use pocketmine\Server;

class TopupRankManager {

    public function grantRank(string $gamertag, string $rank): void {
        $server = Server::getInstance();
        
        $command = "ranksystem setrank $gamertag $rank";

        $server->dispatchCommand($server->getConsoleSender(), $command);

        $player = $server->getPlayerByPrefix($gamertag);
        if ($player !== null) {
            $player->sendMessage("Selamat! Anda telah menerima rank $rank.");
        }
    }

    public function isValidRank(string $rank): bool {
        $ranks = Server::getInstance()->getPluginManager()->getPlugin("RankSystem")->getConfig()->get("ranks", []);
        return in_array($rank, array_keys($ranks), true);
    }
}
