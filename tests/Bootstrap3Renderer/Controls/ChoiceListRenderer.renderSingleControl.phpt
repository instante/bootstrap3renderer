<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\ChoiceListRenderer;
use Mockery\MockInterface;
use Nette\Forms\IControl;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new ChoiceListRenderer($bsr = new BootstrapRenderer, 'fake');
/** @var IControl|MockInterface $ctrl */
$ctrl = mock(IControl::class);
$ctrl->shouldReceive('getControlPart')->with('ctl')->once()->andReturn(Html::el()->addText('The Control'));

$el = $renderer->renderSingleControl($ctrl, 'ctl');
Assert::type(Html::class, $el);
Assert::contains('The Control', (string)$el);



