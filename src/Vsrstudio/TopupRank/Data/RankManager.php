<?php

namespace VsrStudio\TopupRank\Data;

use pocketmine\Server;

class RankManager {

    public function grantRank(string $gamertag, string $rank): void {
        $player = Server::getInstance()->getPlayerByPrefix($gamertag);

        if ($player !== null) {
            $player->sendMessage("Selamat! Anda telah menerima rank $rank.");
        }

        // Logika tambahan jika ada plugin lain yang mengatur rank
    }
}
