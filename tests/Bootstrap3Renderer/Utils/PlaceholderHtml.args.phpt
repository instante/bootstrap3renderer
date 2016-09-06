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
$div->setPlaceholder($span, 'foo');

Assert::exception(function () use ($div) {
    $div->getPlaceholder('non-existent');
}, InvalidStateException::class);
Assert::same($span, $div->getPlaceholder('foo'));

//setPlaceholder(html)
$span2 = Html::el('span');
$div->setPlaceholder($span2);
Assert::same($span2, $div->getPlaceholder());

//setPlaceholder(name)
$div->setPlaceholder('bar');
Assert::same($div, $div->getPlaceholder('bar'));

//setPlaceholder()
$div->setPlaceholder();
Assert::same($div, $div->getPlaceholder());

//implicit placeholder (like calling setPlaceholder() without args on each PlaceholderHtml construction by default)
$div2 = PlaceholderHtml::el('div');
Assert::same($div2, $div2->getPlaceholder());
