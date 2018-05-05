<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Tests\TestBootstrap;
use Nette\Forms\Form;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;
$form->setRenderer(new BootstrapRenderer);
$form->addText('name', 'Your name')->addError('Too short');
$form->addCheckbox('agree', 'I agree');
$form->addImage('picture', '//avatars1.githubusercontent.com/u/13833444?v=3&s=200');
$form->addSelect('sure', 'Are you sure?', ['y' => 'yes', 'n' => 'no']);
$form->addCheckboxList('list', 'List of options', ['a' => 'First', 'b' => 'Second']);
$form->addPassword('password', 'Password')->setRequired();
$form->addButton('btn', 'Push me');
$form->addHidden('you', 'dont see me');
$form->addRadioList('sex', 'Gender', ['m' => 'Male', 'f', 'Female'])->setValue('m')->setDisabled();
$form
    ->addTextArea('text', 'Description')
    ->setValue('Some text')
    ->setOption('description', 'Better be long');
;
$form->addUpload('photo', 'Your photo here');
$form->addSubmit('sub', 'Send me now');
$form->addSubmit('notme', 'But not me')->setDisabled();

ob_start();
require __DIR__ . '/bootstrap.phtml';
$content = ob_get_clean();
file_put_contents(TestBootstrap::$tempDir . '/' . basename(__FILE__) . '.html', simplifyHtmlWhitespaces($content));

ob_start();
$form->render();
$content = ob_get_clean();

Assert::matchFile(__DIR__ . '/expected/simple.htm', simplifyHtmlWhitespaces($content));
