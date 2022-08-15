<?php

namespace App;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;

class ElasticSearch
{
    /**
     * @return Client
     * @throws AuthenticationException
     */
    public static function client(): Client
    {
        return ClientBuilder::create()
            ->setHosts(['http://elasticsearch:9200'])
            ->setBasicAuthentication('elastic', 'vnrXaVMqdK7vRKEdRWN5')
            ->setCABundle(__DIR__ . '/../config/certs/http_ca.crt')
            ->build();
    }
}