<?php

namespace InstanteTests\Bootstrap3Renderer\Controls;

use Instante\Bootstrap3Renderer\Controls\ChoiceListRenderer;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\IControl;

class FakeListRenderer extends ChoiceListRenderer
{
    protected $wrapperClass = 'fake';

    protected function getListItems(IControl $control)
    {
        /** @var CheckboxList $control */
        return $control->getItems();
    }
}

