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
$form->addGroup('G1');
$form->addCheckbox('agree', 'I agree');
$form->addImage('picture', '//avatars1.githubusercontent.com/u/13833444?v=3&s=200');
$form->addGroup('G2');
$form->addSelect('sure', 'Are you sure?', ['y' => 'yes', 'n' => 'no']);
$form->addSubmit('y', 'Send');

// uncomment to dump browser-renderable html to temp dir
//ob_start();
//require __DIR__ . '/bootstrap.phtml';
//$content = ob_get_clean();
//file_put_contents(TestBootstrap::$tempDir . '/' . basename(__FILE__) . '.html', simplifyHtmlWhitespaces($content));

ob_start();
$form->render();
$content = ob_get_clean();

Assert::matchFile(__DIR__ . '/expected/groups.htm', simplifyHtmlWhitespaces($content));


