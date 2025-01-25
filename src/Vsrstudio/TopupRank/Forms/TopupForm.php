<?php

namespace Vsrstudio\TopupRank\Forms;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use Vsrstudio\TopupRank\Main;

class TopupForm {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function getForm(): SimpleForm {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) return;

            $this->showCustomForm($player, $data);
        });

        $form->setTitle("Top-up Rank");
        $form->setContent("Pilih rank yang ingin Anda beli:");
        foreach ($this->plugin->getPluginConfig()["ranks"] as $rank => $price) {
            $form->addButton("$rank\nHarga: Rp$price");
        }

        return $form;
    }

    private function showCustomForm(Player $player, string $rank): void {
        $form = new CustomForm(function (Player $player, $data) use ($rank) {
            if ($data === null) return;

            [$gamertag, $phone, $method] = $data;

            if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
                $player->sendMessage($this->plugin->getPluginConfig()["messages"]["invalid_phone"]);
                return;
            }

            if (empty($gamertag)) {
                $player->sendMessage($this->plugin->getPluginConfig()["messages"]["invalid_gamertag"]);
                return;
            }

            $this->plugin->getOrderManager()->addOrder($gamertag, $rank, $phone, $method);
            $player->sendMessage($this->plugin->getPluginConfig()["messages"]["rank_purchased"]);

            foreach ($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer) {
                if ($onlinePlayer->hasPermission("topuprank.admin")) {
                    $message = str_replace(
                        ["%player%", "%rank%", "%method%"],
                        [$player->getName(), $rank, $method],
                        $this->plugin->getPluginConfig()["messages"]["notify_admin"]
                    );
                    $onlinePlayer->sendMessage($message);
                }
            }
        });

        $form->setTitle("Form Top-up Rank");
        $form->addInput("Masukkan gamertag Anda:", "Contoh: Steve", $player->getName());
        $form->addInput("Masukkan nomor telepon:", "Contoh: 08123456789");
        $form->addDropdown("Pilih metode pembayaran:", $this->plugin->getPluginConfig()["payment_methods"]);

        $player->sendForm($form);
    }
}
