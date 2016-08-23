<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\CheckboxRenderer;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;
use Tester\Environment;

require_once __DIR__ . '/../../bootstrap.php';

$renderer = new CheckboxRenderer($bsr = new BootstrapRenderer);

$form = new Form;
$form->addCheckbox('foo', 'FooBox');
$bsr->renderBegin($form);

$pair = $renderer->renderCheckboxInLabel($form['foo']);
Assert::type(Html::class, $pair);
Assert::same('label', $pair->getName());
Assert::same('input', $pair[0]->getName());
