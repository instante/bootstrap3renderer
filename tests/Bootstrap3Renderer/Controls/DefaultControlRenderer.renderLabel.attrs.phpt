<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\DefaultControlRenderer;
use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new DefaultControlRenderer($bsr = new BootstrapRenderer);
$control = mock(BaseControl::class);
$label = Html::el('label')->setText('foo');
$control->shouldReceive('getLabel')->atLeast()->once()
    ->andReturnUsing(function () use ($label) { return clone $label; });

$rendered = $renderer->renderLabel($control, ['a' => 'b']);
Assert::same('b', $rendered->getAttribute('a'));
