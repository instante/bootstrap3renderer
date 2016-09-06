<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\IControlRenderer;
use Instante\ExtendedFormMacros\PairAttributes;
use Nette\Forms\Form;
use Nette\Forms\IControl;

require_once __DIR__ . '/../bootstrap.php';


$renderer = new BootstrapRenderer;
$renderer->controlRenderers['*'] = $controlRenderer = mock(IControlRenderer::class);
$control = mock(IControl::class);
$pa = new PairAttributes;
$controlRenderer->shouldReceive('renderPair')->with($control, $pa)->once();
$control->shouldReceive('getErrors')->andReturn([]);
$renderer->renderBegin(new Form);
$renderer->renderPair($control, $pa);
