<?php

require 'vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$container = $app->getContainer();

$container['config'] = function () {
    return new Noodlehaus\Config([
        __DIR__ . '/config/cache.php',
    ]);
};

$container['http'] = function () {
    return new \GuzzleHttp\Client;
};

$container['cache'] = function ($c) {
    $client = new \Predis\Client([
        'scheme' => 'tcp',
        'host' => $c->config->get('cache.connections.redis.host'),
        'port' => $c->config->get('cache.connections.redis.port'),
        'password' => $c->config->get('cache.connections.redis.password')
    ]);

    return new \App\Cache\RedisAdapter($client);
};

$app->get('/adrev', function ($request, $response) {
    $revenue = $this->cache->remember('kiwi:adrev', 10, function () {
        $res = $this->http->request('GET', 'https://kiir.us/api.php/?key=2F6E713BD4BA889A21166251DEDE9&cmd=adrev');

        return $res->getBody();
    });

    return $response->withHeader('Content-Type', 'application/json')->write($revenue);
});

$app->run();
