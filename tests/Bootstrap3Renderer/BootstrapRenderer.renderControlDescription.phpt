<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$control = mock(BaseControl::class);
$control->shouldReceive('getOption')->with('description')->twice()->andReturnValues([NULL, 'description']);
$control->shouldReceive('getHtmlId')->andReturn('theid');

$renderer = new BootstrapRenderer;
$desc1 = $renderer->renderControlDescription($control);

Assert::type(Html::class, $desc1);
Assert::same('', (string)$desc1);

$desc2 = $renderer->renderControlDescription($control);
Assert::type(Html::class, $desc2);
Assert::contains('description', (string)$desc2);
Assert::contains('id="describe-theid"', (string)$desc2);
