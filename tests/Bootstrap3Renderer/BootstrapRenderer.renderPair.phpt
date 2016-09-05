<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\ExtendedFormMacros\IControlRenderer;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\InvalidStateException;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


$renderer = new BootstrapRenderer;
$renderer->controlRenderers['*'] = $controlRenderer = mock(IControlRenderer::class);
$control = mock(IControl::class);
$controlRenderer->shouldReceive('renderPair')->with($control)->once();
$control->shouldReceive('getErrors')->andReturn([]);
Assert::exception(function () use ($renderer, $control) {
    $renderer->renderPair($control);
}, InvalidStateException::class, '~No form set~');
$renderer->renderBegin(new Form);
$renderer->renderPair($control);
