<?php

namespace VsrStudio\TopupRank;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use VsrStudio\TopupRank\Forms\TopupForm;
use VsrStudio\TopupRank\Forms\AdminForm;
use VsrStudio\TopupRank\Data\OrderManager;
use VsrStudio\TopupRank\Data\RankManager;

class Main extends PluginBase {

    private OrderManager $orderManager;
    private RankManager $rankManager;
    private array $config;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = $this->getConfig()->getAll();

        $this->orderManager = new OrderManager($this);
        $this->rankManager = new RankManager($this);

                $pluginName = $this->getDescription()->getName();
        $map = $this->getDescription()->getAuthors();
        $ver = $this->getDescription()->getVersion();

        if (isset($map[0])) {
            if ($pluginName !== "TopupRank" or $map[0] !== "VsrStudio" or $ver !== "1.0.1-BETA") {
                $this->getLogger()->emergency("§cPlugin info for $pluginName has been changed. Please restore the correct plugin.yml: author should be \"VsrStudio\", version should be \"1.0.1-BETA\", and plugin name should be \"TopupRank\".");
                $this->getServer()->shutdown();
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "topuprank") {
            if ($sender instanceof Player) {
                if (!$sender->hasPermission("topuprank.use")) {
                    $sender->sendMessage("§cAnda tidak memiliki izin untuk menggunakan perintah ini.");
                    return true;
                }

                $form = new TopupForm($this);
                $sender->sendForm($form->getForm());
            } else {
                $sender->sendMessage("Perintah ini hanya dapat digunakan oleh pemain.");
            }
            return true;
        }

        if ($command->getName() === "rankadmin") {
            if ($sender instanceof Player) {
                if (!$sender->hasPermission("topuprank.admin")) {
                    $sender->sendMessage("§cAnda tidak memiliki izin untuk menggunakan perintah ini.");
                    return true;
                }

                $form = new AdminForm($this);
                $sender->sendForm($form->getForm());
            } else {
                $sender->sendMessage("Perintah ini hanya dapat digunakan oleh pemain.");
            }
            return true;
        }

        return false;
    }

    public function getOrderManager(): OrderManager {
        return $this->orderManager;
    }

    public function getRankManager(): RankManager {
        return $this->rankManager;
    }

    public function getPluginConfig(): array {
        return $this->config;
    }
}
