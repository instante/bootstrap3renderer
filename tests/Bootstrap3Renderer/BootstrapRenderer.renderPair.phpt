<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Bootstrap3Renderer\ScreenSizeEnum;
use Nette\Forms\IControl;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$control = mock(IControl::class);

/** @return BootstrapRenderer */
function getPartialMockRenderer($control)
{
    $mock = mock(BootstrapRenderer::class
        . '[renderControl,renderControlDescription,renderControlErrors,renderLabel]');
    $mock->shouldReceive('renderControl')->with($control, TRUE)->once()->andReturn('[ctrl]');
    $mock->shouldReceive('renderLabel')->with($control)->once()->andReturn('[label]');
    $mock->shouldReceive('renderControlErrors')->with($control)->once()->andReturn('[errors]');
    $mock->shouldReceive('renderControlDescription')->with($control)->once()->andReturn('[desc]');
    return $mock;
}

/** @noinspection PhpUndefinedMethodInspection */
$el = getPartialMockRenderer($control)->renderPair($control);
Assert::type('string', $el);
Assert::contains('[ctrl]', $el);
Assert::contains('[label]', $el);
Assert::contains('[errors]', $el);
Assert::contains('[desc]', $el);
Assert::contains('class="form-group"', $el);

Assert::notContains('col-md-9',
    getPartialMockRenderer($control)
        ->setRenderMode(RenderModeEnum::VERTICAL)
        ->setLabelColumns(3)
        ->setColumnMinScreenSize(ScreenSizeEnum::MD)
        ->renderPair($control)
);

Assert::contains('col-md-9',
    getPartialMockRenderer($control)
        ->setRenderMode(RenderModeEnum::HORIZONTAL)
        ->setLabelColumns(3)
        ->setColumnMinScreenSize(ScreenSizeEnum::MD)
        ->renderPair($control)
);

