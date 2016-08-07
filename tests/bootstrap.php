<?php

require_once __DIR__ . '/../vendor/autoload.php';
\Instante\Tests\TestBootstrap::prepareTestEnvironment(__DIR__ . '/tmp/' . substr(md5(uniqid()), 0, 10));
