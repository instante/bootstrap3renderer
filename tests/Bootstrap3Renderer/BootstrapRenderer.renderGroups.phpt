<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;
$renderer = new BootstrapRenderer;
$form->setRenderer($renderer);
$renderer->renderBegin($form);

$form->addGroup('First');
$form->addText('foo');
$form->addText('bar');
$form->addGroup('Second');
$form->addText('baz');
$form->addGroup('Third');
$form->addGroup('Fourth')->setOption('visual', FALSE);
$form->addText('barbaz');

$rendered = $renderer->renderGroups();
Assert::type(Html::class, $rendered);
$renderedStr = (string)$rendered;
Assert::contains('First', $renderedStr);
Assert::contains('foo', $renderedStr);
Assert::contains('bar', $renderedStr);
Assert::contains('Second', $renderedStr);
Assert::contains('baz', $renderedStr);
Assert::notContains('Third', $renderedStr); //skipped empty group
Assert::notContains('Fourth', $renderedStr); //skipped non-visual group



