<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Foundation\Console\Kernel")->bootstrap();

try {
    $view = \Illuminate\Support\Facades\View::make("volt-livewire::pages::settings.security", []);
    echo "Got view: " . $view->name() . PHP_EOL;
} catch (\Exception $e) {
    echo get_class($e) . ": " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo "Trace:" . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
