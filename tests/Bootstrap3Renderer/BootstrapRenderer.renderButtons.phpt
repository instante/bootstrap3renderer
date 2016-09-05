<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Bootstrap3Renderer\ScreenSizeEnum;
use Nette\Forms\Controls\Button;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class FakeButton extends Button
{
    private $text;

    public function __construct($text)
    {
        parent::__construct();
        $this->text = $text;
    }

    public function getControl($caption = NULL)
    {
        return Html::el()->addText($this->text);
    }
}

$form = new Form;

$renderer = new BootstrapRenderer;
$renderer->renderBegin($form); //to fetch form object

//empty render
$empty = $renderer->renderButtons([]);
Assert::type(Html::class, $empty);
Assert::same('', (string)$empty);

//horizontal render
$renderer->setRenderMode(RenderModeEnum::HORIZONTAL)
    ->setColumnMinScreenSize(ScreenSizeEnum::LG)
    ->setLabelColumns(4);
$verticalRender = $renderer->renderButtons([new FakeButton('{Foo}'), new FakeButton('{Bar}')]);
Assert::type(Html::class, $verticalRender);
$verticalRenderStr = (string)$verticalRender;
Assert::match('~{Foo}\s+{Bar}~', $verticalRenderStr);
Assert::contains('col-lg-8', $verticalRenderStr);
Assert::contains('col-lg-offset-4', $verticalRenderStr);

//other render
$renderer->setRenderMode(RenderModeEnum::VERTICAL);
$normalRender = $renderer->renderButtons([new FakeButton('{Foo}'), new FakeButton('{Bar}')]);
Assert::type(Html::class, $normalRender);
Assert::match('~^{Foo}\s+{Bar}$~', (string)$normalRender);
