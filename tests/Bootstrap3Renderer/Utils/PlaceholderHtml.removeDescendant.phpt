<?php

namespace InstanteTests\Bootstrap3Renderer\Utils;

use Instante\Bootstrap3Renderer\Utils\PlaceholderHtml;
use Nette\InvalidStateException;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// setPlaceholder(Html, name)
$div = PlaceholderHtml::el('div');
$div->addHtml($p = Html::el('p'));
$p->addHtml($span = Html::el('span'));
$p->addHtml($span);
$p->addHtml($span);
$div->setPlaceholder($span, 'foo');

$div->removeDescendant($span);
Assert::count(2, $p->getChildren());

$div->addHtml($span);
$div->removeDescendant($span, TRUE);
Assert::count(0, $p->getChildren());
Assert::count(1, $div->getChildren());
