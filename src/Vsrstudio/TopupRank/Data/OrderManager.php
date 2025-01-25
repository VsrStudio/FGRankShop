<?php

namespace Vsrstudio\TopupRank\Data;

use pocketmine\plugin\Plugin;

class OrderManager {

    private Plugin $plugin;
    private string $filePath;
    private array $orders;

    public function __construct(Plugin $plugin) {
        $this->plugin = $plugin;
        $this->filePath = $plugin->getDataFolder() . "orders.json";

        if (!file_exists($this->filePath)) {
            $this->orders = [];
            $this->save();
        } else {
            $this->orders = json_decode(file_get_contents($this->filePath), true);
        }
    }

    public function addOrder(string $gamertag, string $rank, string $phone, string $method): void {
        $this->orders[] = [
            "gamertag" => $gamertag,
            "rank" => $rank,
            "phone" => $phone,
            "method" => $method,
            "time" => date("Y-m-d H:i:s")
        ];
        $this->save();
    }

    public function getOrders(): array {
        return $this->orders;
    }

    public function approveOrder(array $order): void {
        $this->removeOrder($order);
        $this->logOrder($order, "approved");
    }

    public function rejectOrder(array $order): void {
        $this->removeOrder($order);
        $this->logOrder($order, "rejected");
    }

    private function removeOrder(array $order): void {
        $this->orders = array_filter($this->orders, fn($o) => $o !== $order);
        $this->save();
    }

    private function logOrder(array $order, string $status): void {
        $logFile = $this->plugin->getDataFolder() . "order_log.txt";
        $logEntry = "[" . date("Y-m-d H:i:s") . "] " .
            "Gamertag: " . $order["gamertag"] . ", " .
            "Rank: " . $order["rank"] . ", " .
            "Metode: " . $order["method"] . ", " .
            "Status: " . $status . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    private function save(): void {
        file_put_contents($this->filePath, json_encode($this->orders));
    }
}
