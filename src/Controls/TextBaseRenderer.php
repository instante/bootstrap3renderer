<?php

namespace Instante\Bootstrap3Renderer\Controls;

use Nette\Forms\IControl;
use Nette\Utils\Html;

class TextBaseRenderer extends DefaultControlRenderer
{
    const FORM_CONTROL_CLASS = 'form-control';

    /** @inheritdoc */
    public function renderControl(IControl $control, array $attrs = [], $part = NULL, $renderedDescription = FALSE)
    {
        /** @var Html $el */
        $el = parent::renderControl($control, $attrs, $part, $renderedDescription);
        if ($el->getName() !== '' && !isset($attrs['no-form-control'])) {
            $el->appendAttribute('class', static::FORM_CONTROL_CLASS);
        }
        return $el;
    }
}
