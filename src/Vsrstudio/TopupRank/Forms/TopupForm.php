<?php

namespace VsrStudio\TopupRank\Forms;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use VsrStudio\TopupRank\Main;

class TopupForm {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function getForm(): CustomForm {
        $form = new CustomForm(function (Player $player, array $data) {
            if ($data === null) return;

            $rank = $data[0];
            $paymentMethod = $data[1];

            $rankPrice = $this->plugin->getPluginConfig()["ranks"][$rank];
            if (!$this->plugin->getRankManager()->hasRank($player, $rank)) {
                if ($paymentMethod === "Saldo In-Game" && $player->getBalance() >= $rankPrice) {
                    $player->reduceBalance($rankPrice);
                    $this->plugin->getOrderManager()->addOrder($player->getName(), $rank, $paymentMethod);
                    $this->plugin->getRankManager()->grantRank($player, $rank);
                    $player->sendMessage($this->plugin->getPluginConfig()["messages"]["rank_purchased"]);
                } else if ($paymentMethod === "Transfer Bank") {
                    $this->plugin->getOrderManager()->addOrder($player->getName(), $rank, $paymentMethod);
                    $this->sendAdminNotification($rank, $player->getName(), $paymentMethod);
                    $player->sendMessage($this->plugin->getPluginConfig()["messages"]["rank_purchased"]);
                } else {
                    $player->sendMessage($this->plugin->getPluginConfig()["messages"]["insufficient_funds"]);
                }
            } else {
                $player->sendMessage($this->plugin->getPluginConfig()["messages"]["already_has_rank"]);
            }
        });

        $form->setTitle("Top-Up Rank");
        $form->addLabel("Pilih rank yang ingin dibeli:");
        $form->addDropDown("Rank", array_keys($this->plugin->getPluginConfig()["ranks"]));
        $form->addDropDown("Pilih Metode Pembayaran", $this->plugin->getPluginConfig()["payment_methods"]);

        return $form;
    }

    private function sendAdminNotification(string $rank, string $playerName, string $paymentMethod): void {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $admin) {
            if ($admin->hasPermission("topuprank.admin")) {
                $admin->sendMessage(str_replace(
                    ["%player%", "%rank%", "%method%"],
                    [$playerName, $rank, $paymentMethod],
                    $this->plugin->getPluginConfig()["messages"]["notify_admin"]
                ));
            }
        }
    }
}
