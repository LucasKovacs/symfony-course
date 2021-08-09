<?php

declare (strict_types = 1);

use App\Container;
use App\Controller\IndexController;
use App\Format\XML;
use App\Service\Serializer;

require __DIR__ . '/../vendor/autoload.php';

print("Simple Service Container\n\n");

$data = [
    'name' => 'John',
    'surname' => 'Doe',
];

$serializer = new Serializer(new XML());
$controller = new IndexController($serializer);

$container = new Container();

$container->addService('format.json', function () use ($container) {
    return new JSON();
});

$container->addService('format.xml', function () use ($container) {
    return new XML();
});

$container->addService('format', function () use ($container) {
    return $container->getService('format.json');
});

$container->addService('serializer', function () use ($container) {
    return new Serializer($container->getService('format'));
});

$container->addService('controller.index', function () use ($container) {
    return new IndexController($container->getService('serializer'));
});

var_dump($container->getServices());
var_dump($container->getService('controller.index')->index());
