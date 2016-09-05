<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\ExtendedFormMacros\PairAttributes;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class PairsTestingBootstrapRenderer extends BootstrapRenderer
{
    public function renderPairs($controls)
    {
        return parent::renderPairs($controls);
    }

    public function renderPair(IControl $control, PairAttributes $attrs = NULL)
    {
        return $control->getValue();
    }

    public function renderButtons(array $buttons)
    {
        return implode(':', array_map(function (IControl $button) {
            return $button->getValue();
        }, $buttons));
    }
}

interface IOptionControl
{
    public function getOption();
}

function fakeButton($value)
{
    $m = spy(IControl::class, IOptionControl::class);
    $m->shouldReceive('getOption')->with('type')->andReturn('button')->atLeast()->once();
    $m->shouldReceive('getValue')->andReturn($value);
    return $m;
}

function fakeControl($value)
{
    $m = spy(IControl::class);
    $m->shouldReceive('getValue')->andReturn($value);
    return $m;
}

$form = new Form;

$renderer = new PairsTestingBootstrapRenderer;
$renderer->renderBegin($form); //to fetch form object

$controls = [
    fakeControl('first'),
    fakeControl('second'),
    fakeButton('b1'),
    fakeButton('b2'),
    fakeControl('third'),
    fakeButton('b3'),
];

Assert::match('~first\s*second\s*b1:b2\s*third\s*b3~', (string)$renderer->renderPairs($controls));
