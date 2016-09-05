<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\IControlRenderer;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


$renderer = new BootstrapRenderer;
$renderer->controlRenderers['*'] = $controlRenderer = mock(IControlRenderer::class);
$control = spy(BaseControl::class);
$controlRenderer->shouldReceive('renderPair')->with($control, NULL)->andReturnUsing(function () {
    return Html::el('div');
});
$control->shouldReceive('isRequired')->andReturnValues([TRUE, FALSE]);
$renderer->renderBegin(new Form);

$element = $renderer->renderPair($control);
Assert::contains('required', (string)$element);

$element = $renderer->renderPair($control);
Assert::notContains('required', (string)$element);
