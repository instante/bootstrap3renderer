<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Nette\Forms\Form;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;

$renderer = new BootstrapRenderer;
Assert::same('<form action="" method="post">', $renderer->renderBegin($form));

$renderer->setRenderMode(RenderModeEnum::HORIZONTAL);
Assert::same('<form action="" method="post" class="form-horizontal">', $renderer->renderBegin($form));

$renderer->setRenderMode(RenderModeEnum::INLINE);
Assert::same('<form action="" method="post" class="form-inline">', $renderer->renderBegin($form));

Assert::match('~class=(?=.*\bform-inline\b)(?=.*\bfoo\b)~', $renderer->renderBegin($form, ['class' => 'foo'])); //has both form-inline and foo class

Assert::same('<form action="" method="post">', $renderer->renderBegin($form, ['class' => 'no-form-inline']));
