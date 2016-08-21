<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Bootstrap3Renderer\ScreenSizeEnum;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$renderer = new BootstrapRenderer;
//submit button
$submit = $renderer->renderButton(new SubmitButton('FooCaption'));
Assert::type(Html::class, $submit);
$submitStr = (string)$submit;
Assert::contains('FooCaption', $submitStr);
Assert::contains('btn-primary', $submitStr);
Assert::notContains('btn-default', $submitStr);

$button = (string)$renderer->renderButton(new Button('BarCaption'));
Assert::contains('btn-default', $button);
Assert::notContains('btn-primary', $button);

$b2 = new Button('BarCaption');
$b2->getControlPrototype()->appendAttribute('class', 'btn-warning');
$button2 = (string)$renderer->renderButton(new Button('baz'));
Assert::contains('btn-warning', $button2);
Assert::notContains('btn-primary', $button2);
Assert::notContains('btn-default', $button2);
