<?php

namespace App\modules;

use App\Boot;
use App\ElasticSearch;
use Exception;

class DefaultHashDocument
{
    public string $index = "";
    protected Boot $application;
    protected string $enhancedIndex;

    public function __construct(Boot $application)
    {
        $this->application = $application;
        $this->enhancedIndex = $this->application->appId . "-" . $this->index;
    }

    public function get(int|string $id): array
    {
        try {
            $result = ElasticSearch::client()
                ->get([
                    'index' => $this->enhancedIndex,
                    'id' => $id
                ])
                ->asArray();

            $document = $result["_source"];
            $document["id"] = $result["_id"];
            $document["version"] = $result["_version"];

            return $document;


        } catch (Exception) {
            return [
                "error" => true,
                "message" => "Not found.",
                "code" => 404
            ];
        }
    }

    public function delete(int|string $id): array
    {
        try {
            ElasticSearch::client()
                ->delete([
                    'index' => $this->enhancedIndex,
                    'id' => $id
                ]);

            return [
                "success" => true
            ];
        } catch (Exception) {
            return [
                "error" => true,
                "message" => "Not found.",
                "code" => 404
            ];
        }
    }

    public function create(object $body): array
    {
        try {
            $response = ElasticSearch::client()
                ->index([
                    'index' => $this->enhancedIndex,
                    'body' => $body
                ])->asObject();
        } catch (Exception $e) {
            return [
                "error" => true,
                "message" => $e->getMessage(),
                "code" => 500
            ];
        }

        return [
            "success" => true,
            "id" => $response->_id
        ];
    }

    public function update(object $body, string|int $id): array
    {
        try {

            ElasticSearch::client()
                ->update([
                    'index' => $this->enhancedIndex,
                    'id' => $id,
                    'body' => [
                        'doc' => $body
                    ]
                ]);


        } catch (Exception $e) {
            return [
                "error" => true,
                "message" => $e->getMessage(),
                "code" => 500
            ];
        }

        return [
            "success" => true
        ];
    }

}