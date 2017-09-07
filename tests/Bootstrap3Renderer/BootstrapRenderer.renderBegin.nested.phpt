<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Form;
use Nette\InvalidStateException;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;
$renderer = new BootstrapRenderer;

$renderer->renderBegin($form);
Assert::exception(function() use ($form, $renderer) {
    $renderer->renderBegin($form);
}, InvalidStateException::class);

$renderer->reset();

Assert::noError(function() use ($form, $renderer) {
    $renderer->render($form);
    $renderer->renderBegin($form);
    $renderer->renderEnd();
});
