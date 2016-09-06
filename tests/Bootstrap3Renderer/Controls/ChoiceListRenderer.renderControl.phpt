<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\ChoiceListRenderer;
use Mockery\MockInterface;
use Nette\Forms\IControl;
use Nette\Utils\Html;
use Tester\Assert;

interface IGetItems
{
    public function getItems();
}

require_once __DIR__ . '/../../bootstrap.php';

$items = ['a' => 'Item1', 'b' => 'Item2', 'c' => 'Item3'];

$renderer = new ChoiceListRenderer($bsr = new BootstrapRenderer, 'fake');
/** @var IControl|MockInterface $ctrl */
$ctrl = mock(IControl::class, IGetItems::class);

$ctrl->shouldReceive('getItems')->atLeast()->once()->andReturn($items);
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

