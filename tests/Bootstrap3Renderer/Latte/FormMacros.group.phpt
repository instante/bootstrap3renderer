<?php

use InstanteTests\Boostrap3Renderer\Latte\MacroTester;
use Nette\Forms\ControlGroup;

require __DIR__ . '/../../bootstrap.php';
require __DIR__ . '/MacroTester.inc';

$tester = new MacroTester('{form theForm}{group Foo}{/form}');

$tester->getForm()->addGroup('Foo');

$tester->getMockRenderer()->shouldReceive('renderGroup')->withArgs(function (ControlGroup $group) {
    return $group->getOption('label') === 'Foo';
})->once();
$tester->render();
