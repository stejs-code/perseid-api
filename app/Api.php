<?php

namespace App;

use Error;

class Api
{
    private array $response;
    private string $contentType;
    private object $module;
    private array $user;

    public function __construct(Boot $application)
    {
        $this->response = [
            "version" => $application->version
        ];
        $this->contentType = "application/json";

        if ($application->error["error"]) {
            $this->contentType = "application/json";
            $this->response = $application->error;
            return;
        }
//        Developer::dump($application);

        $this->user = $application->user;


        try {
            $module = "\\App\\modules\\" . $application->uri[1] . "\\" . ucfirst($application->uri[2]);
            $this->module = new $module($application);
        } catch (Error) {
            $this->contentType = "application/json";
            $this->response = [
                "error" => true,
                "message" => "Endpoint does not exist.",
                "code" => 403
            ];
            return;
        }

//        Developer::dump($this->user);

        if (
            !isset($this->user["permissions"][$application->uri[1]][$application->uri[2]][strtolower($application->method)])
            or
            !$this->user["permissions"][$application->uri[1]][$application->uri[2]][strtolower($application->method)]
        ) {

            $this->contentType = "application/json";
            $this->response = [
                "error" => true,
                "message" => "You don't have permissions to do this.",
                "code" => 403
            ];
            return;

        }

//        if (
//            isset($application->uri[3])
//            and
//            ($application->method === "GET")
//            and
//            ($application->uri[3] === "search")
//        ) {
//            //TODO: Search
//
//        }

        if (
            isset($application->uri[3])
            and
            ($application->method === "GET")
        ) {
            $this->response = $this->module->get($application->uri[3]);
        }

        if (
            isset($application->uri[3])
            and
            ($application->method === "DELETE")
        ) {
            $this->response = $this->module->delete($application->uri[3]);
        }

        if (
            isset($application->body)
            and
            ($application->method === "POST")
        ) {
            $this->response = $this->module->create($application->body);
        }

        if (
            isset($application->uri[3])
            and
            isset($application->body)
            and
            ($application->method === "PUT")
        ) {
            $this->response = $this->module->update($application->body, $application->uri[3]);
        }

    }

    public function getResponse($took): string
    {
        header('Content-Type: ' . $this->contentType);

        if (isset($_GET["dev"])) {
            $this->response["took"] = $took;
        }

        return (string)json_encode($this->response);
    }
}