<?php

declare(strict_types=1);

namespace NgLamVN\AntiToolBox;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener {

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
	}

	/**
	 * @param DataPacketReceiveEvent $event
	 * @priority NORMAL
	 * @ignoreCancelled TRUE
	 */
	public function onRecieve(DataPacketReceiveEvent $event) {
		$player = $event->getOrigin();
		$packet = $event->getPacket();
		if ($packet instanceof LoginPacket) {
			$clientDataJwt = JwtUtils::parse($packet->clientDataJwt)[1];
			$deviceOS = $clientDataJwt["DeviceOS"];
			$deviceModel = $clientDataJwt["DeviceModel"];
			if ($deviceOS !== DeviceOS::ANDROID) {
				return;
			}
			/**
			 * Something about device model check, for example:
			 * Original client: XIAOMI Note 8 Pro
			 * Toolbox client: Xiaomi Note 8 Pro
			 *
			 * Original client: SAMSUNG SM-A105F
			 * Toolbox client: samsung SM-A105F
			 */
			$name = explode(" ", $deviceModel);
			if (!isset($name[0])) {
				return;
			}
			$check = $name[0];
			$check = strtoupper($check);
			if ($check !== $name[0]) {
				$player->disconnect($this->getConfig()->get("kickMessage"));
			}
		}
	}
}
