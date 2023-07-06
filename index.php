<?php

require_once 'vendor/autoload.php';

use Src\Enums\BrowserEnum;
use Src\WebdriverManager;

function dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function dd($var)
{
    dump($var);
    die();
}

$whoops = new \Whoops\Run;
$whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

(new WebdriverManager)
    ->downloadTo(BrowserEnum::CHROME, __DIR__ . '/chromedriver', true)
    ->extractTo(__DIR__ . '/chromedriver');
