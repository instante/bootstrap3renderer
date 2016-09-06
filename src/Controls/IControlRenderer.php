<?php

namespace Instante\Bootstrap3Renderer\Controls;

use Instante\ExtendedFormMacros\PairAttributes;
use Nette\Forms\IControl;
use Nette\Utils\Html;

interface IControlRenderer
{
    /**
     * @param IControl $control
     * @param PairAttributes $attrs
     * @return Html
     */
    public function renderPair(IControl $control, PairAttributes $attrs = NULL);

    /**
     * @param IControl $control
     * @param array $attrs
     * @param string $part
     * @param bool $renderedDescription if control description was or will be rendered too
     *  (for linking the description by Html element id)
     * @return Html
     */
    public function renderControl(IControl $control, array $attrs = [], $part = NULL, $renderedDescription = FALSE);

    /**
     * @param IControl $control
     * @param array $attrs
     * @param string $part
     * @return Html
     */
    public function renderLabel(IControl $control, array $attrs = [], $part = NULL);
}
