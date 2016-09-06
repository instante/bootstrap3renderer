<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\CheckboxRenderer;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new CheckboxRenderer($bsr = new BootstrapRenderer);

$form = new Form;
$form->addCheckbox('foo', 'FooBox');
$bsr->renderBegin($form);

$checkbox = $renderer->renderCheckboxInLabel($form['foo']);
Assert::type(Html::class, $checkbox);
Assert::same('label', $checkbox->getName());
Assert::same('input', $checkbox[0]->getName());
Assert::contains('FooBox', (string)$checkbox);
