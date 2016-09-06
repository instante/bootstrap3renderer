<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\ChoiceListRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Mockery\MockInterface;
use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new ChoiceListRenderer($bsr = new BootstrapRenderer, 'fake');
/** @var BaseControl|MockInterface $ctrl */
$ctrl = mock(BaseControl::class); //BaseControl mock needed to see getOption() existing

class RenderOptions
{
    public static $renderInline = FALSE;
    public static $noRenderInline = FALSE;
}

$ctrl->shouldReceive('getControlPart')->with('ctl')->andReturn(Html::el()->addText('The Control'));
$ctrl->shouldReceive('getLabelPart')->with('ctl')->andReturn(Html::el('label')->addText('The Label'));
$ctrl->shouldReceive('getOption')->with('renderInline')->andReturnUsing(function () {
    return RenderOptions::$renderInline;
});
$ctrl->shouldReceive('getOption')->with('noRenderInline')->andReturnUsing(function () {
    return RenderOptions::$noRenderInline;
});
/** @var Html $el */
$el = $renderer->renderSingleChoice($ctrl, 'ctl');
Assert::type(Html::class, $el);
Assert::contains('The Control', (string)$el);
Assert::contains('The Label', (string)$el);
Assert::contains('class="fake"', (string)$el);
Assert::same('div', $el->getName());

//render as inline by render mode
$bsr->setRenderMode(RenderModeEnum::INLINE);
$el = $renderer->renderSingleChoice($ctrl, 'ctl');
Assert::contains('class="fake-inline"', (string)$el);
Assert::same('label', $el->getName());

//suppress inline render by option
RenderOptions::$noRenderInline = TRUE;
$el = $renderer->renderSingleChoice($ctrl, 'ctl');
Assert::contains('class="fake"', (string)$el);
Assert::same('div', $el->getName());

//enable inline render by option
$bsr->setRenderMode(RenderModeEnum::VERTICAL);
RenderOptions::$noRenderInline = FALSE;
RenderOptions::$renderInline = TRUE;
$el = $renderer->renderSingleChoice($ctrl, 'ctl');
Assert::contains('class="fake-inline"', (string)$el);
Assert::same('label', $el->getName());

