<?php

namespace Instante\Bootstrap3Renderer\Controls;

use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\ExtendedFormMacros\PairAttributes;
use Instante\Helpers\SecureCallHelper;
use Nette\Forms\IControl;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;

class CheckboxRenderer extends DefaultControlRenderer
{

    /**
     * div.form-group
     * [div.col-{ScreenSize}-offset-{LabelColumns}.col-{ScreenSize}-{InputColumns}]
     *     div.checkbox
     *         label
     *             input type="checkbox"
     *             ...Label
     *
     * @param IControl $control
     * @param PairAttributes $attrs
     * @return Html
     */
    public function renderPair(IControl $control, PairAttributes $attrs = NULL)
    {
        $attrs = $attrs ?: new PairAttributes;

        $r = $this->bootstrapRenderer;
        $pair = clone $r->getPrototypes()->pair;

        $label = $this->renderCheckboxInLabel($control, $attrs);
        $cb = Html::el('div', ['class' => 'checkbox']);
        $cb->addHtml($label);
        $wrapper = $this->wrapControlInColumnsGrid($pair, $cb);
        if ($r->getRenderMode() === RenderModeEnum::HORIZONTAL) {
            $wrapper->appendAttribute('class', $r->getOffsetClass($r->getLabelColumns()));
        }

        $pair->getPlaceholder('control')->addHtml($wrapper);

        $pair->getPlaceholder('errors')->addHtml($r->renderControlErrors($control));
        $pair->getPlaceholder('description')->addHtml($r->renderControlDescription($control));
        $pair->addAttributes($attrs->container);
        return $pair;
    }

    /** @inheritdoc */
    public function renderCheckboxInLabel(IControl $control, PairAttributes $attrs = NULL)
    {
        $attrs = $attrs ?: new PairAttributes;

        $label = $this->renderLabel($control, $attrs->label);
        $controlHtml = $this->renderControl($control, $attrs->input);
        $label->insert(0, $controlHtml);
        return $label;
    }

    /** @inheritdoc */
    public function renderControl(IControl $control, array $attrs = [], $part = NULL, $renderedDescription = FALSE)
    {
        if ($part !== NULL) {
            throw new InvalidArgumentException(__CLASS__ . ' does not support rendering control parts');
        }
        if (!method_exists($control, 'getControlPart')) {
            return Html::el();
        }
        $r = $this->bootstrapRenderer;
        /** @var Html $el */
        $el = $control->getControlPart(); // the dirty Nette way to get <input type=checkbox> only from checkbox
        if ($el instanceof Html) {
            if ($renderedDescription && $r->getControlDescription($control) !== NULL) {
                $el->setAttribute('aria-describedby', $r->getDescriptionId($control));
            }
            $el->addAttributes($attrs);
        } else {
            $el = Html::el()->addHtml($el);
        }
        return $el;
    }

    protected function getControlLabel(IControl $control, $part = NULL)
    {
        return SecureCallHelper::tryCall($control, 'getLabelPart');
    }
}
