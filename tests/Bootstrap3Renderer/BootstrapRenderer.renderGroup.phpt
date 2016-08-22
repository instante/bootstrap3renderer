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

// has label but not description

$form->addGroup('First');
$form->addText('foo');
$form->addText('bar');

$r1 = $renderer->renderGroup($form->getGroup('First'));
Assert::type(Html::class, $r1);
$r1str = (string)$r1;
Assert::contains('<legend>First</legend>', $r1str);
Assert::notContains('<p', $r1str);
Assert::contains('foo', $r1str);
Assert::contains('bar', $r1str);

// has description but no label

$form->addGroup('Second')->setOption('label', '')->setOption('description', 'The Very Second');
$form->addText('baz');
$r2str = (string)$renderer->renderGroup($form->getGroup('Second'));
Assert::notContains('<legend>', $r2str);
Assert::match('~<p[^>]*>The Very Second~', $r2str);


// passed attributes from group container

$form->addGroup('Third')->setOption('container', Html::el()->addAttributes(['class' => 'klasse']));
$form->addText('barbaz');
$r3str = (string)$renderer->renderGroup($form->getGroup('Third'));
Assert::contains('class="klasse"', $r3str);
