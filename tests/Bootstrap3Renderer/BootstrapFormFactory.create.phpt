<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapFormFactory;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Form;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class CustomForm extends Form
{
}

$factory = new BootstrapFormFactory;

// basic Nette\Forms\Form
$form1 = $factory->create();
Assert::same(Form::class, get_class($form1)); //must be Form exactly, no descendant

// Nette\Application\UI\Form used if present
require_once __DIR__ . '/FakeUIForm.php';
$form2 = $factory->create();
Assert::type(\Nette\Application\UI\Form::class, $form2);

// custom form class
$form3 = $factory->create(CustomForm::class);
Assert::type(CustomForm::class, $form3);
Assert::type(BootstrapRenderer::class, $form3->getRenderer());
