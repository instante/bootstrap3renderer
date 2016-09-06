<?php

function recursiveClear(&$arr)
{
    if (array_key_exists('~CLEAR', $arr)) {
        foreach ($arr['~CLEAR'] as $clearKey) {
            unset($arr[$clearKey]);
        }
        unset($arr['~CLEAR']);
    }
    foreach ($arr as $key => &$val) {
        if (is_array($val)) {
            recursiveClear($val);
        }
    }
}

$rootDir = __DIR__ . '/..';
$testsDir = __DIR__;

$variant = getenv('COMPOSER_PATCH') ?: 'default';
$composerFile = $rootDir . '/composer.json';
$composerJson = json_decode(file_get_contents($composerFile), TRUE);
if ($variant !== 'default') {
    $composerPatchFile = $rootDir . '/tests/composer/' . $variant . '.json';
    $composerPatchJson = json_decode(file_get_contents($composerPatchFile), TRUE);
    $composerJson = array_replace_recursive($composerJson, $composerPatchJson);
    recursiveClear($composerJson);
    echo 'Using composer configuration variant ' . $variant;
} else {
    echo 'Using default composer configuration';
}
file_put_contents($composerFile, json_encode($composerJson));
