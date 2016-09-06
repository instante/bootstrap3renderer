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

$form->addError('globalFoo');
$form->addText('erroneous')->addError('ctrlFoo');
$globalHtml = $renderer->renderGlobalErrors(TRUE);
Assert::type(Html::class, $globalHtml);
$global = (string)$globalHtml;
Assert::contains('globalFoo', $global);
Assert::notContains('ctrlFoo', $global);

$all = (string)$renderer->renderGlobalErrors(FALSE);
Assert::contains('globalFoo', $all);
Assert::contains('ctrlFoo', $all);

$form->addError('<br>');
Assert::contains('&lt;br&gt;', (string)$renderer->renderGlobalErrors(TRUE));

$form->addError(Html::el('hr'));
Assert::contains('<hr', (string)$renderer->renderGlobalErrors(TRUE));
