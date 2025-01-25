<?php

namespace Vsrstudio\TopupRank\Data;

use Vsrstudio\TopupRank\Main;

class ApiKeyManager {

    private Main $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * Membuat API Key baru dan menyimpannya
     *
     * @return string API Key yang baru dibuat
     */
    public function createAPIKey(): string {
        $apiKey = bin2hex(random_bytes(16));

        $this->saveAPIKey($apiKey);

        return $apiKey;
    }

    /**
     * Menyimpan API Key ke dalam konfigurasi
     *
     * @param string $apiKey API Key yang akan disimpan
     */
    private function saveAPIKey(string $apiKey): void {
        $this->plugin->getConfig()->set("api_key", $apiKey);
        $this->plugin->saveConfig();
    }

    /**
     * Mengambil API Key yang disimpan dalam konfigurasi
     *
     * @return string|null API Key yang disimpan atau null jika tidak ada
     */
    public function getAPIKey(): ?string {
        return $this->plugin->getConfig()->get("api_key", null);
    }

    /**
     * Memverifikasi apakah API Key yang diberikan valid
     *
     * @param string $apiKey API Key yang akan diverifikasi
     * @return bool True jika API Key valid, false jika tidak
     */
    public function verifyAPIKey(string $apiKey): bool {
        $storedApiKey = $this->getAPIKey();

        return $storedApiKey === $apiKey;
    }
}
