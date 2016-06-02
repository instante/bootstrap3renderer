<?php

$rootDir = __DIR__ . '/..';
$testsDir = __DIR__;

if (getenv('NETTE') !== 'default') {
    $composerFile = $rootDir . '/composer.json';
    $composerJson = json_decode(file_get_contents($composerFile), TRUE);
    foreach ($composerJson['require'] as $key => &$val) {
        if (preg_match('~^(?:nette|latte|tracy)/~', $key)) {
            $val = getenv('NETTE');
        }
    }
    file_put_contents($composerFile, json_encode($composerJson));

    echo 'Using nette components version ' . getenv('NETTE');

} else {
    echo 'Using default nette version';
}
