<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\DefaultControlRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Bootstrap3Renderer\ScreenSizeEnum;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$control = mock(IControl::class);

/**
 * @param $control
 * @param BootstrapRenderer $bsrMock
 * @return DefaultControlRenderer
 */
function getPartialMockRenderer($control, BootstrapRenderer &$bsrMock = NULL)
{
    /** @var BootstrapRenderer $bsrMock */
    $mock = mock(DefaultControlRenderer::class . '[renderControl,renderLabel]', [
        $bsrMock = mock(BootstrapRenderer::class . '[renderControlErrors,renderControlDescription]'),
    ]);
    $bsrMock->controlRenderers['*'] = $mock;
    $mock->shouldReceive('renderControl')->with($control, [], NULL, TRUE)->once()->andReturn(Html::el()->addText('[ctrl]'));
    $mock->shouldReceive('renderLabel')->with($control, [])->once()->andReturn('[label]');
    $bsrMock->shouldReceive('renderControlErrors')->with($control)->once()->andReturn('[errors]');
    $bsrMock->shouldReceive('renderControlDescription')->with($control)->once()->andReturn('[desc]');
    $bsrMock->renderBegin(new Form);
    return $mock;
}

/** @noinspection PhpUndefinedMethodInspection */
$el = getPartialMockRenderer($control)->renderPair($control);
Assert::type(Html::class, $el);
$el = (string)$el;
Assert::contains('[ctrl]', $el);
Assert::contains('[label]', $el);
Assert::contains('[errors]', $el);
Assert::contains('[desc]', $el);
Assert::contains('class="form-group"', $el);

$crMock = getPartialMockRenderer($control, $bsrMock);
/** @var BootstrapRenderer $bsrMock */
$bsrMock->setRenderMode(RenderModeEnum::VERTICAL)
    ->setLabelColumns(3)
    ->setColumnMinScreenSize(ScreenSizeEnum::MD);
Assert::notContains('col-md-9', (string)$crMock->renderPair($control));

$crMock = getPartialMockRenderer($control, $bsrMock);
/** @var BootstrapRenderer $bsrMock */
$bsrMock->setRenderMode(RenderModeEnum::HORIZONTAL)
    ->setLabelColumns(3)
    ->setColumnMinScreenSize(ScreenSizeEnum::MD);
Assert::contains('col-md-9', (string)$crMock->renderPair($control));

