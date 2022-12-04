<?php

declare(strict_types=1);

namespace shunnyan\discord;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use shunnyan\discord\discordclient;

class Main extends PluginBase implements Listener
{
	public static $discordurl_status;
	public static $sche;
	public static $webhookurl_main = "";
	public function onEnable(): void
	{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		date_default_timezone_set('Asia/Tokyo');
		self::$sche = $this->getScheduler();
		self::$discordurl_status = new Config($this->getDataFolder() . "discord.yml", Config::YAML, [
			"discord_status_url"     => "",
		]);
		if (self::$discordurl_status->get("discord_status_url") == "") {
			$emc = new embedcreater();
			$emc->reserched();
		}
		discordclient::ondata();
	}
	public function onJoin(PlayerJoinEvent $event)
	{
		discordclient::ondata();
	}
	public function onLeave(PlayerQuitEvent $event)
	{
		self::$sche->scheduleDelayedTask(new ClosureTask(function (): void {
			discordclient::ondata();
		}), 20);
	}
	public function onDisable(): void
	{
		discordclient::ondata(null, "stop");
	}

	public function whitelist(CommandEvent $event)
	{
		$name = strtolower($event->getCommand());
		if (str_contains($name, 'whitelist off')) {
			discordclient::ondata("無効");
		}
		if (str_contains($name, 'whitelist on')) {
			discordclient::ondata("有効");
		}
	}
}
