<?php

namespace InstanteTests\Bootstrap3Renderer\Utils;

use Instante\Bootstrap3Renderer\Utils\PlaceholderHtml;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$div = PlaceholderHtml::el('div');
$div->setPlaceholder('foo')->setPlaceholder('bar');

$div = clone $div;

Assert::same($div, $div->getPlaceholder());
Assert::same($div, $div->getPlaceholder('foo'));
Assert::same($div, $div->getPlaceholder('bar'));
