<?php

namespace VsrStudio\TopupRank\Forms;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use VsrStudio\TopupRank\Main;

class AdminForm {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function getForm(): SimpleForm {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $apiKey = $this->plugin->getAPIKeyManager()->getAPIKey();
                    if ($apiKey === null) {
                        $player->sendMessage("§eAPI Key belum dibuat. Silakan restart server.");
                    } else {
                        $player->sendMessage("§aAPI Key Anda: §b$apiKey");
                    }
                    break;

                case 1:
                    $this->showOrderList($player);
                    break;

                default:
                    break;
            }
        });

        $form->setTitle("Menu Admin - Topup Rank");
        $form->setContent("Pilih opsi yang tersedia:");
        $form->addButton("Lihat API Key");
        $form->addButton("Daftar Permintaan");

        return $form;
    }

    private function showOrderList(Player $player): void {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) return;

            $orders = $this->plugin->getOrderManager()->getOrders();
            if (isset($orders[$data])) {
                $order = $orders[$data];
                $this->showOrderDetails($player, $order);
            }
        });

        $form->setTitle("Daftar Permintaan");
        $orders = $this->plugin->getOrderManager()->getOrders();

        if (empty($orders)) {
            $form->setContent("§cTidak ada permintaan top-up saat ini.");
        } else {
            foreach ($orders as $index => $order) {
                $form->addButton("Gamertag: " . $order["gamertag"] . "\nRank: " . $order["rank"] . "\nMetode: " . $order["method"]);
            }
        }

        $player->sendForm($form);
    }

    private function showOrderDetails(Player $player, array $order): void {
        $form = new SimpleForm(function (Player $player, $data) use ($order) {
            if ($data === null) return;

            switch ($data) {
                case 0:
                    $this->plugin->getOrderManager()->approveOrder($order);
                    $this->plugin->getRankManager()->grantRank($order["gamertag"], $order["rank"]);
                    $player->sendMessage("§aPermintaan disetujui. Rank telah diberikan kepada pemain.");
                    break;

                case 1:
                    $this->plugin->getOrderManager()->rejectOrder($order);
                    $player->sendMessage("§cPermintaan ditolak.");
                    break;
            }
        });

        $form->setTitle("Detail Permintaan");
        $form->setContent(
            "Gamertag: " . $order["gamertag"] . "\n" .
            "Nomor Telepon: " . $order["phone"] . "\n" .
            "Rank: " . $order["rank"] . "\n" .
            "Metode Pembayaran: " . $order["method"] . "\n" .
            "Waktu: " . $order["time"]
        );
        $form->addButton("Setujui");
        $form->addButton("Tolak");

        $player->sendForm($form);
    }
}
