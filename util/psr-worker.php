<?php
$autoloader = require __DIR__ . '/../vendor/autoload.php';

$worker = new Spiral\RoadRunner\Worker(new Spiral\Goridge\StreamRelay(STDIN, STDOUT));
$client = new Spiral\RoadRunner\PSR7Client($worker);

$app = App\AppFactory::create($autoloader, [
    App\Settings::BASE_DIR => dirname(__DIR__),
]);

while ($request = $client->acceptRequest()) {
    try {
        $response = $app->handle($request);
        $client->respond($response);
    } catch (Throwable $e) {
        $client->getWorker()->error((string)$e);
    }

    $entityManager->clear();
    $entityManager->getConnection()->close();
    gc_collect_cycles();
}