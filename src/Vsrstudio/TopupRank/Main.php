<?php

namespace VsrStudio\TopupRank;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use VsrStudio\TopupRank\Forms\TopupForm;
use VsrStudio\TopupRank\Forms\AdminForm;
use VsrStudio\TopupRank\Data\OrderManager;
use VsrStudio\TopupRank\Data\RankManager;

class Main extends PluginBase implements Listener {

    private OrderManager $orderManager;
    private RankManager $rankManager;
    private array $config;

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = $this->getConfig()->getAll();

        $this->orderManager = new OrderManager($this);
        $this->rankManager = new RankManager($this);
        $this->getLogger()->info("TopupRank v1.1-BETA diaktifkan. Thx To VsrStudio");
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

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {
            case "topuprank":
                if ($sender instanceof Player) {
                    $form = new TopupForm($this);
                    $sender->sendForm($form->getForm());
                } else {
                    $sender->sendMessage("Perintah ini hanya dapat digunakan oleh pemain.");
                }
                break;

            case "rankadmin":
                if ($sender->hasPermission("topuprank.admin")) {
                    $form = new AdminForm($this);
                    if ($sender instanceof Player) {
                        $sender->sendForm($form->getForm());
                    } else {
                        $sender->sendMessage("Gunakan perintah ini dalam game.");
                    }
                } else {
                    $sender->sendMessage("Anda tidak memiliki izin untuk menggunakan perintah ini.");
                }
                break;

            default:
                return false;
        }
        return true;
    }
}
