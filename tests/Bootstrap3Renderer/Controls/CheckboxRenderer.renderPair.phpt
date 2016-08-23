<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\Controls\CheckboxRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Mockery\MockInterface;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$form = new Form;
$form->addCheckbox('foo', 'FooBox')->setOption('description', '[desc]');

$renderer = mock(CheckboxRenderer::class . '[renderCheckboxInLabel]',
    [$bsr = mock(BootstrapRenderer::class . '[renderControlErrors]')]);
/** @var CheckboxRenderer|MockInterface $renderer */
/** @var BootstrapRenderer|MockInterface $bsr */

$renderer->shouldReceive('renderCheckboxInLabel')->with($form['foo'])->andReturn('[checkboxInLabel]');
$bsr->shouldReceive('renderControlErrors')->with($form['foo'])->andReturn('[errors]');

$bsr->renderBegin($form);

$bsr->setRenderMode(RenderModeEnum::VERTICAL);
$pair = $renderer->renderPair($form['foo']);
Assert::type(Html::class, $pair);
Assert::same('form-group', $pair->getAttribute('class'));

// .checkbox wrapper
Assert::same('checkbox', $pair[0]->getAttribute('class'));

$rendered = (string)$pair;

// label and input rendered
Assert::contains('[checkboxInLabel]', $rendered);

// test rendered errors and description
Assert::contains('[errors]', $rendered);
Assert::contains('[desc]', $rendered);

// TODO test column wrapper in and only in horizontal mode
Assert::notContains('col-', (string)$pair);
$bsr->setRenderMode(RenderModeEnum::HORIZONTAL);
Assert::contains('col-', (string)$renderer->renderPair($form['foo']));

