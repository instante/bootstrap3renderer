<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\IControlRenderer;
use Nette\Forms\Controls\BaseControl;

require_once __DIR__ . '/../bootstrap.php';

$renderer = new BootstrapRenderer;
$renderer->controlRenderers['*'] = $mcr = spy(IControlRenderer::class);
$control = spy(BaseControl::class);
$mcr->shouldReceive('renderLabel')->with($control)->once();
$renderer->renderLabel($control);
