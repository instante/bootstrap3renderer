<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$renderer = new BootstrapRenderer;
$form = new Form;
$form->setRenderer($renderer);
$renderer->renderBegin($form);

$container = mock(Container::class);
$container->shouldReceive('getControls')->withNoArgs()->once()->andReturn([spy(BaseControl::class)]);

$html = $renderer->renderContainer($container);

Assert::type(Html::class, $html);
