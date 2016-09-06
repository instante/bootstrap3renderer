<?php

/**
 * Verifies that Nette\Bridges\FormsLatte\Runtime::renderFormBegin() was used by $renderer->renderBegin()
 */

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Tests\Utils\MockStatic;
use Mockery;
use Nette\Bridges\FormsLatte\Runtime;
use Nette\Forms\Form;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;

MockStatic::mock(Runtime::class)->shouldReceive('renderFormBegin')->with($form, [], Mockery::any())->once()->andReturn('[BEGIN]');

$renderer = new BootstrapRenderer;
Assert::same('[BEGIN]', $renderer->renderBegin($form));
