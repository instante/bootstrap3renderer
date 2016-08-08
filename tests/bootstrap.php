<?php

require_once __DIR__ . '/../vendor/autoload.php';
\Instante\Tests\TestBootstrap::prepareTestEnvironment(__DIR__ . '/tmp/'
    . (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('ymd_His'));
