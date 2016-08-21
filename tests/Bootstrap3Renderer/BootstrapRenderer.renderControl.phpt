<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$renderer = new BootstrapRenderer;
$form = new Form;

$control = spy(BaseControl::class);
$control->shouldReceive('getControl')->andReturn(Html::el('input')->setAttribute('value', 'theCtrl'));
$control->shouldReceive('getHtmlId')->andReturn('myid');
$control->shouldReceive('getOption')->with('description')->andReturn('myid');
$control->shouldReceive('getRules->check');
$control->shouldReceive('getForm')->andReturn($form);
$form->addComponent($control, 'a');
$renderer->renderBegin($form);

// renderControl prevents rendering twice
Assert::contains('theCtrl', $renderer->renderBody());
Assert::notContains('theCtrl', $renderer->renderBody());

Assert::type(Html::class, $renderer->renderControl($control));
Assert::contains('class="form-control"', (string)$renderer->renderControl($control));
Assert::contains('aria-describedby="describe-myid"', (string)$renderer->renderControl($control, TRUE));

