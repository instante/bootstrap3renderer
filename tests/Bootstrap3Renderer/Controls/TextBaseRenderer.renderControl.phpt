<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\TextBaseRenderer;
use Nette\Forms\Controls\TextBase;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new TextBaseRenderer(new BootstrapRenderer);
$form = new Form;

$control = spy(TextBase::class);
$control->shouldReceive('getControl')->andReturn(Html::el('input')->setAttribute('value', 'theCtrl'));
$control->shouldReceive('getRules->check');
$control->shouldReceive('getForm')->andReturn($form);
$control->shouldReceive('getErrors')->andReturn([]);
$form->addComponent($control, 'a');

Assert::contains('class="form-control"', (string)$renderer->renderControl($control));

