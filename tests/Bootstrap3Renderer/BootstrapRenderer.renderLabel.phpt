<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Bootstrap3Renderer\ScreenSizeEnum;
use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$renderer = new BootstrapRenderer;
$control = mock(BaseControl::class);
$label = Html::el('label')->setText('foo');
$control->shouldReceive('getLabel')->atLeast()->once()
    ->andReturnUsing(function () use ($label) { return clone $label; });

$renderer->setRenderMode(RenderModeEnum::VERTICAL);
$rendered = $renderer->renderLabel($control);
Assert::type('string', $rendered);
Assert::contains('foo', $rendered);

$renderer->setRenderMode(RenderModeEnum::HORIZONTAL);
$renderer->setLabelColumns(3);
$renderer->setColumnMinScreenSize(ScreenSizeEnum::SM);
$rendered = $renderer->renderLabel($control);
Assert::type('string', $rendered);
Assert::contains('foo', $rendered);
Assert::null($label->getAttribute('class')); //label's attribute was not written into prototype
Assert::contains('col-sm-3', $rendered);
