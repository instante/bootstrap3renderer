<?php

namespace InstanteTests\Bootstrap3Renderer\Utils;

use Instante\Bootstrap3Renderer\Utils\PlaceholderHtml;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

$div = PlaceholderHtml::el('div');
$div->addHtml($p = Html::el('p'));
$p->addHtml($span = Html::el('span'));
$div->setPlaceholder($span);

$div->getPlaceholder()->setText('Hello');

Assert::contains('Hello', (string)$div);

$div2 = clone $div;
$div2->getPlaceholder()->setText('world');
Assert::contains('world', (string)$div2);
Assert::contains('Hello', (string)$div);
Assert::notContains('world', (string)$div);

$div3 = PlaceholderHtml::el('div');
$div3c = clone $div3;
Assert::same($div3c, $div3c->getPlaceholder());
