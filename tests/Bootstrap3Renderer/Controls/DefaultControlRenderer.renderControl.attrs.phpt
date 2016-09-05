<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\DefaultControlRenderer;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new DefaultControlRenderer(new BootstrapRenderer);
$form = new Form;

$control = spy(BaseControl::class);
$control->shouldReceive('getControl')->andReturn(Html::el('input')->setAttribute('value', 'theCtrl'));
$control->shouldReceive('getHtmlId')->andReturn('myid');
$control->shouldReceive('getOption')->with('description')->andReturn('myid');
$control->shouldReceive('getRules->check');
$control->shouldReceive('getForm')->andReturn($form);
$control->shouldReceive('getErrors')->andReturn([]);
$form->addComponent($control, 'a');

Assert::same('b', $renderer->renderControl($control, ['a' => 'b'])->getAttribute('a'));

