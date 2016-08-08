<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Tests\Utils\MockStatic;
use Nette\Bridges\FormsLatte\Runtime;
use Nette\Forms\Form;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;

$mockRuntime = MockStatic::mock(Runtime::class);
$mockRuntime->shouldReceive('renderFormBegin');
$mockRuntime->shouldReceive('renderFormEnd')->with($form)->once()->andReturn('[END]');

$renderer = new BootstrapRenderer;
$renderer->renderBegin($form);
Assert::same('[END]', $renderer->renderEnd());
