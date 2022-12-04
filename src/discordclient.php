<?php

declare(strict_types=1);

namespace shunnyan\discord;


class discordclient{
	public function ondata($white = null,$stop = null) : void{
		date_default_timezone_set('Asia/Tokyo');
		$url = Main::$discordurl_status->get("discord_status_url");
		$headers = [ 'Content-Type: application/json' ];
		$data = new embedcreater();
		$hookObject = $data->statusdata($white,$stop);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"PATCH");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $hookObject);
		$response   = curl_exec($ch);
	}
}
