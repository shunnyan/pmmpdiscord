<?php

namespace shunnyan\discord;

use pocketmine\Server;

class embedcreater {

    public function reserched() {
        date_default_timezone_set('Asia/Tokyo');
        $url = Main::$webhookurl_main . "?wait=true";
        $headers = ['Content-Type: application/json'];
        $text = date('Y-m-d\TH:i:sO');
        $hookObject = json_encode([
            "username" => "Server Status",
            "tts" => false,
            "embeds" => [
                [
                    "title" => "Server Status",
                    "timestamp" => $text,
                    "color" => hexdec("000000"),
                    "fields" => [
                        [
                            "name" => "データ取得中...",
                            "value" => "メッセージを保存中...",
                            "inline" => false
                        ]
                    ]
                ]
            ]
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $hookObject);
        $response = curl_exec($ch);
        if (!isset($response))
            return;
        $decode = json_decode($response, true);
        Main::$discordurl_status->set("discord_status_url", Main::$webhookurl_main . "/messages/" . $decode["id"]);
        Main::$discordurl_status->save();
    }

    public function statusdata($white, $stop) {
        $color = "0aff03";
        $servertps = Server::getInstance()->getTicksPerSecond();
        if ($white == null) {
            $whitelistenable = Server::getInstance()->hasWhitelist();
            if ($whitelistenable == 1) {
                $white = "有効";
            }
            if ($whitelistenable == 0) {
                $white = "無効";
            }
        } else {
            if ($white == "on") {
                $white = "有効";
            }
            if ($white == "off") {
                $white = "無効";
            }
        }
        if ($white == "有効") {
            $color = "ff8000";
        }
        $onp = Server::getInstance()->getOnlinePlayers();
        $count = count($onp);
        date_default_timezone_set('Asia/Tokyo');
        $text = date('Y-m-d\TH:i:sO');
        if ($stop == null) {
            $hookObject = json_encode([
                "username" => "Server Status",
                "tts" => false,
                "embeds" => [
                    [
                        "title" => "Server Status",
                        "timestamp" => $text,
                        "color" => hexdec($color),
                        "fields" => [
                            [
                                "name" => "サーバーステータス",
                                "value" => "ONLINE",
                                "inline" => false
                            ],
                            [
                                "name" => "ホワイトリスト",
                                "value" => "$white",
                                "inline" => false
                            ],
                            [
                                "name" => "人数",
                                "value" => "$count",
                                "inline" => true
                            ],
                            [
                                "name" => "TPS",
                                "value" => "$servertps",
                                "inline" => true
                            ]
                        ]
                    ]
                ]
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } elseif ($stop == "stop") {
            $hookObject = json_encode([
                "username" => "Server Status",
                "tts" => false,
                "embeds" => [
                    [
                        "title" => "Server Status",
                        "timestamp" => $text,
                        "color" => hexdec("ff0000"),
                        "fields" => [
                            [
                                "name" => "サーバーステータス",
                                "value" => "OFFLINE",
                                "inline" => false
                            ]
                        ]
                    ]
                ]
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $hookObject = json_encode([
                "username" => "Server Status",
                "tts" => false,
                "embeds" => [
                    [
                        "title" => "Server Status",
                        "timestamp" => $text,
                        "color" => hexdec("ff0000"),
                        "fields" => [
                            [
                                "name" => "サーバーステータス",
                                "value" => "ERROR",
                                "inline" => false
                            ]
                        ]
                    ]
                ]
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return $hookObject;
    }

}
