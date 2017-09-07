<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Form;
use Nette\InvalidArgumentException;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;
$renderer = new BootstrapRenderer;
Assert::exception(function() use ($form, $renderer) {
    $renderer->render($form, 'begin');
}, InvalidArgumentException::class);
