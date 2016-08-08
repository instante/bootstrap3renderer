<?php

use InstanteTests\Boostrap3Renderer\Latte\MacroTester;
use Nette\Forms\Container;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{container foo}{/form}');

$tester->getForm()->addContainer('foo');

$tester->getMockRenderer()->shouldReceive('renderContainer')->withArgs(function (Container $container) {
    return $container->getName() === 'foo';
})->once();
$tester->render();
