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
$control->shouldReceive('getRules->check');
$control->shouldReceive('getForm')->andReturn($form);
$control->shouldReceive('getErrors')->andReturn([]);
$form->addComponent($control, 'a');
$renderer->renderBegin($form);

// renderControl prevents rendering twice
Assert::contains('theCtrl', $renderer->renderBody());
Assert::notContains('theCtrl', $renderer->renderBody());


