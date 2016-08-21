<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Controls\Button;

use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;
$submitFooButton = new SubmitButton('FooCaption');
$barButton = new Button('BarCaption');
$renderer = new BootstrapRenderer;
$form->addComponent($submitFooButton, 'foo')
    ->addComponent($barButton, 'bar');

//render submit button as btn-primary
$submit = $renderer->renderButton($submitFooButton);
Assert::type(Html::class, $submit);
$submitStr = (string)$submit;
Assert::contains('FooCaption', $submitStr);
Assert::contains('btn-primary', $submitStr);
Assert::notContains('btn-default', $submitStr);

//render common button as btn-default
$button = (string)$renderer->renderButton($barButton);
Assert::contains('btn-default', $button);
Assert::notContains('btn-primary', $button);

//render button already having btn-* class
$barButton->getControlPrototype()->appendAttribute('class', 'btn-warning');
$button2 = (string)$renderer->renderButton($barButton);
Assert::contains('btn-warning', $button2);
Assert::notContains('btn-primary', $button2);
Assert::notContains('btn-default', $button2);
