<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use InstanteTests\Bootstrap3Renderer\Controls\FakeListRenderer;
use Mockery\MockInterface;
use Nette\Forms\IControl;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/FakeRenderer.inc';

$items = ['a' => 'Item1', 'b' => 'Item2', 'c' => 'Item3'];

$renderer = new FakeListRenderer($bsr = new BootstrapRenderer);
/** @var IControl|MockInterface $ctrl */
$ctrl = mock(IControl::class);
$ctrl->shouldReceive('getItems')->atLeast()->once()->andReturn(array_keys($items));
$ctrl->shouldReceive('getLabelPart')->atLeast()->once()->andReturnUsing(function ($x) {
    return Html::el()->addText($x);
});
$ctrl->shouldReceive('getControlPart')->atLeast()->once()->andReturnUsing(function ($x) use ($items) {
    return Html::el()->addText($items[$x]);
});


$el = $renderer->renderControl($ctrl);
Assert::type(Html::class, $el);
Assert::contains('Item1', (string)$el);
Assert::contains('Item2', (string)$el);

$renderer->separator = Html::el()->addText('separator');
Assert::same(2, substr_count((string)$renderer->renderControl($ctrl), 'separator')); //length-1 separators

