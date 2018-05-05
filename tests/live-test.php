<?php

require_once __DIR__ . '/../vendor/autoload.php';

$form = new \Nette\Forms\Form();
$renderer = new Instante\Bootstrap3Renderer\BootstrapRenderer(
    \Instante\Bootstrap3Renderer\RenderModeEnum::HORIZONTAL
);
$form->setRenderer($renderer);

$form->addText('foo', 'Foo')
    ->setOption('description', 'Better be long');
$form->addText('baz', 'Baz')
    ->addError('Stuff happened');
$form->addCheckbox('bar', 'Bar')->setDefaultValue(1);
$form->addSubmit('submit', 'Send');

$form->fireEvents();

require __DIR__ . '/Integration/bootstrap.phtml';

