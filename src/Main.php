<?php

declare(strict_types=1);

namespace shunnyan\discord;

use DateTime;
use DateTimeZone;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;
use shunnyan\test\Sends;

class Main extends PluginBase implements Listener
{
	public static $discordurl_status;
	public discordbots $thread;
	public static $sche;
	public static $webhookurl_main = "https://discord.com/api/webhooks/1000097326699905126/X-QLjwdFO_JiFZvFb87LkOgeXBhTuUx1mzTF6JnfjBthNnwdGzI_63jsKAVwcDa08T8w";
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
		$dsc = new discordclient();
		$dsc->ondata();
		$this->thread = new discordbots();
		$this->thread->start(PTHREADS_INHERIT_NONE);
		$this->getScheduler()->scheduleRepeatingTask(new discordseirias($this->thread), 10);
	}
	public function onJoin(PlayerJoinEvent $event)
	{
		$dsc = new discordclient();
		$dsc->ondata();
	}
	public function onLeave(PlayerQuitEvent $event)
	{
		self::$sche->scheduleDelayedTask(new ClosureTask(function (): void {
			$dsc = new discordclient();
			$dsc->ondata();
		}), 20);
	}
	public function onDisable(): void
	{
		$dsc = new discordclient();
		$dsc->ondata(null, "stop");
	}

	public function whitelist(CommandEvent $event)
	{
		$name = strtolower($event->getCommand());
		if (str_contains($name, 'whitelist off')) {
			$dsc = new discordclient();
			$dsc->ondata("無効");
		}
		if (str_contains($name, 'whitelist on')) {
			$dsc = new discordclient();
			$dsc->ondata("有効");
		}
	}
	public function chat(PlayerChatEvent $event)
	{
		$caht = $event->getMessage();
		$plname = $event->getPlayer()->getName();
		$cahts = "<{$plname}> {$caht}";
		$this->thread->sendToThread($cahts); //プロパティより使用
	}
}
class discordseirias extends Task
{

	public function __construct(protected discordbots $thread)
	{
	}

	public function onRun(): void
	{
		foreach ($this->thread->fetchOutData() as $key) {
			foreach ($key as $tya) {
				$tyas = mb_substr($tya, ($iti = (mb_strpos($tya, 'r') + 1)), (mb_strpos($tya, '§a§b§c§d§e§r')) - $iti);
				if (str_starts_with($tyas, '!')) {
					$tyas = str_replace("!", "", $tyas);
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "$tyas");
				} elseif (str_starts_with($tyas, '>')) {
					$tyas = str_replace(">", "", $tyas);
					if ($tyas == "userlist") {
						$usli = "ユーザーリスト:";
						foreach (Server::getInstance()->getOnlinePlayers() as $ky) {
							$usli .= "\n" . $ky->getName();
						}
						$this->thread->sendToThread($usli);
					}
				} else {
					$today = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
					//$tya = str_replace("§", "", $tya);
					Server::getInstance()->broadcastMessage("<§bDiscord§r> $tya");
				}
			}
		}
	}
}
