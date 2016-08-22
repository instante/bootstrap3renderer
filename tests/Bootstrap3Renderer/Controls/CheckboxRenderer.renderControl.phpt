<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\CheckboxRenderer;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new CheckboxRenderer(new BootstrapRenderer);
$form = new Form;

$control = spy(Checkbox::class);
$control->shouldReceive('getControlPart')->andReturn(Html::el('input')->setAttribute('value', '1'));
$form->addComponent($control, 'a');

$ctrl = $renderer->renderControl($control);
/** @var Html $ctrl */
Assert::type(Html::class, $ctrl);
Assert::same('input', $ctrl->getName());

