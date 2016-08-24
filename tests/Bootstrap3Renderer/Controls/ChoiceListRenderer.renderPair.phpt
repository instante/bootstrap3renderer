<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use InstanteTests\Bootstrap3Renderer\Controls\FakeListRenderer;
use Mockery\MockInterface;
use Nette\Forms\IControl;
use Nette\Utils\Html;
use Tester\Assert;
use Tester\Environment;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/FakeRenderer.inc';

$renderer = new FakeListRenderer($bsr = new BootstrapRenderer);
/** @var IControl|MockInterface $ctrl */
$ctrl = mock(IControl::class);

Environment::skip('todo');
//$ctrl->shouldReceive('getControlPart')->with('ctl')->andReturn(Html::el()->addText('The Control'));

//$el = $renderer->renderSingleControl($ctrl, 'ctl');
//Assert::type(Html::class, $el);
//Assert::contains('The Control', (string)$el);



