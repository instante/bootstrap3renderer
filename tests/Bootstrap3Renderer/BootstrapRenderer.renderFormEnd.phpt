<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Form;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$form = new Form;

$renderer = new BootstrapRenderer;
$renderer->renderBegin($form);
Assert::match('~</form>$~', $renderer->renderEnd());
