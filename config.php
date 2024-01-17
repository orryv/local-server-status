<?php

    // env variables
    const SERVER_CONFIG = [
        'HTTPS' => [
            'ssl' => true,
            'host' => 'localhost',
            'port' => 443,
            'expectedStatusCode' => 302,
        ],
        'HTTP' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 80,
            'expectedStatusCode' => 302,
        ],
        'MySQL' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 3306,
            'expectedStatusCode' => 200,
        ],
        'ElasticSearch' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 9200,
            'expectedStatusCode' => 200,
        ],
        'ElasticSearch Kibana' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 5601,
            'expectedStatusCode' => 200,
        ],
        'MongoDB' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 27017,
            'expectedStatusCode' => 200,
        ],
        'MongoDB Express' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 8081,
            'expectedStatusCode' => 200,
        ],
        'Redis' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 6379,
            'expectedStatusCode' => 200,
        ],
        'Redis Insight' => [
            'ssl' => false,
            'host' => 'localhost',
            'port' => 8001,
            'expectedStatusCode' => 200,
        ],
    ];

?>