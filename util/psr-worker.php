<?php
ini_set('display_errors', 'stderr');
error_reporting(E_ALL);
chdir(__DIR__);
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

require __DIR__ . '/../vendor/autoload.php';

$worker = new RoadRunner\Worker(new Goridge\StreamRelay(STDIN, STDOUT));
$psr7 = new RoadRunner\PSR7Client($worker);

$app = App\AppFactory::create($autoloader, [
    App\Settings::BASE_DIR => dirname(__DIR__),
]);

$app->run();

while ($request = $psr7->acceptRequest()) {
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