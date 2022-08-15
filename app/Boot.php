<?php

namespace App;

use Exception;

class Boot
{
    public string $fullPath;
    public string $version;
    public array $uri;
    public array $error = [
        "error" => false
    ];
    public string $appId;
    public string $method;
    public array $user;
    public object|null $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = json_decode(file_get_contents('php://input'));

        $this->fullPath = substr(strtok($_SERVER["REQUEST_URI"], "?"), 1);
        $this->uri = explode("/", $this->fullPath);
        $this->version = floatval(substr($this->uri[0], 1));
        unset($this->uri[0]);


        if (
            !isset($_GET["app"])
            or
            !isset($_GET["token"])
        ) {
            $this->error = [
                "error" => true,
                "message" => '"app" and "token" are not set in query',
                "code" => 401
            ];
            return;
        }


        try {

            $user = ElasticSearch::client()
                ->get([
                    "index" => $_GET["app"] . "-users",
                    "id" => explode("&", $_GET["token"])[0]
                ])
                ->asArray()["_source"];

        } catch (Exception $e) {
            if (isset($_GET["dev"])) {
                $this->error = [
                    "error" => true,
                    "message" => $e->getMessage(),
                    "code" => 401
                ];
                return;
            }

            $this->error = [
                "error" => true,
                "message" => "Wrong application id or token.",
                "code" => 401
            ];
            return;
        }

        $this->appId = $_GET["app"];
        $this->user = $user;

        if ($this->user["token"] !== explode("&", $_GET["token"])[1]) {
            $this->error = [
                "error" => true,
                "message" => "Wrong token.",
                "code" => 401
            ];
        }
    }
}