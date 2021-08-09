<?php declare (strict_types = 1);

use App\Kernel;

require __DIR__ . '/../vendor/autoload.php';

//print("Autowired Service Container\n\n");

$kernel = (new Kernel)->boot();
$kernel->handleRequest();
