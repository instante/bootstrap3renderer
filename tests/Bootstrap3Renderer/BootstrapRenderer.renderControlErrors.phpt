<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

$control = new TextInput;
$control->addError('<error1>');
$control->addError('error2');
$control->addError(Html::el('span'));

$renderer = new BootstrapRenderer;
$errorsHtml = $renderer->renderControlErrors($control);
Assert::type(Html::class, $errorsHtml);
$errors = (string)$errorsHtml;
Assert::contains('&lt;error1&gt;', $errors);
Assert::contains('error2', $errors);
Assert::contains('<span', $errors);
