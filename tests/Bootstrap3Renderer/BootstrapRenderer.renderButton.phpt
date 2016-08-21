<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Bootstrap3Renderer\ScreenSizeEnum;
use Nette\Forms\Controls\Button;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;

$renderer = new BootstrapRenderer;
$renderer->renderBegin($form); //to fetch form object

//empty render
$empty = $renderer->renderButtons([]);
Assert::type(Html::class, $empty);
Assert::same('', (string)$empty);

//horizontal render
$renderer->setRenderMode(RenderModeEnum::HORIZONTAL)
    ->setColumnMinScreenSize(ScreenSizeEnum::LG)
    ->setLabelColumns(4);
$verticalRender = $renderer->renderButtons([new FakeButton('[Foo]'), new FakeButton('[Bar]')]);
Assert::type(Html::class, $verticalRender);
$verticalRenderStr = (string)$verticalRender;
Assert::contains('[Foo][Bar]', $verticalRenderStr);
Assert::contains('col-lg-8', $verticalRenderStr);
Assert::contains('col-lg-offset-4', $verticalRenderStr);

//other render
$renderer->setRenderMode(RenderModeEnum::VERTICAL);
$normalRender = $renderer->renderButtons([new FakeButton('[Foo]'), new FakeButton('[Bar]')]);
Assert::type(Html::class, $normalRender);
Assert::same('[Foo][Bar]', (string)$normalRender);
