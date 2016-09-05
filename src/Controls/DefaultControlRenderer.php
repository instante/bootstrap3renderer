<?php

namespace Instante\Bootstrap3Renderer\Controls;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Bootstrap3Renderer\Utils\PlaceholderHtml;
use Instante\ExtendedFormMacros\IControlRenderer;
use Instante\Helpers\SecureCallHelper;
use Nette\Forms\IControl;
use Nette\Utils\Html;

class DefaultControlRenderer implements IControlRenderer
{
    /** @var BootstrapRenderer */
    protected $bootstrapRenderer;

    public function __construct(BootstrapRenderer $bootstrapRenderer)
    {
        $this->bootstrapRenderer = $bootstrapRenderer;
    }

    /** @inheritdoc */
    public function renderPair(IControl $control)
    {
        $r = $this->bootstrapRenderer;

        $pair = clone $r->getPrototypes()->pair;
        $pair->getPlaceholder('label')->addHtml($this->renderLabel($control));
        $ctrlHtml = $this->renderControl($control, TRUE);
        /** @var Html $ctrlHtml */
        $wrapper = $this->wrapControlInColumnsGrid($pair, $ctrlHtml);

        $pair->getPlaceholder('control')->addHtml($wrapper);
        $pair->getPlaceholder('errors')->addHtml($r->renderControlErrors($control));
        $pair->getPlaceholder('description')->addHtml($r->renderControlDescription($control));
        return $pair;
    }

    /**
     * Wraps control in col-SS-IC when rendering in horizontal mode.
     *
     * WARNING of side effect - replaces 'errors' and 'description' placeholders if they were kept at $pair by default.
     *
     * @param PlaceholderHtml $pair
     * @param Html $control
     * @return Html
     */
    protected function wrapControlInColumnsGrid(PlaceholderHtml $pair, Html $control)
    {
        $r = $this->bootstrapRenderer;
        if ($r->getRenderMode() === RenderModeEnum::HORIZONTAL) {
            // wrap in bootstrap columns
            $columns = Html::el('div')
                ->appendAttribute('class', $r->getColumnsClass($r->getInputColumns()))
                ->addHtml($control);
            if ($pair->getPlaceholder('errors') === $pair) {
                $pair->setPlaceholder($columns, 'errors');
            }
            if ($pair->getPlaceholder('description') === $pair) {
                $pair->setPlaceholder($columns, 'description');
            }
            return $columns;
        } else {
            return $control;
        }
    }

    /** @inheritdoc */
    public function renderControl(IControl $control, $renderedDescription = FALSE)
    {
        if (!method_exists($control, 'getControl')) {
            return Html::el();
        }
        $r = $this->bootstrapRenderer;
        /** @var Html $el */
        $el = $control->getControl();
        if ($el instanceof Html) {
            $el->appendAttribute('class', $r::FORM_CONTROL_CLASS);
            if ($renderedDescription && $r->getControlDescription($control) !== NULL) {
                $el->setAttribute('aria-describedby', $r->getDescriptionId($control));
            }
        } else {
            $el = Html::el()->addHtml($el);
        }
        return $el;
    }

    /** @inheritdoc */
    public function renderLabel(IControl $control)
    {
        $r = $this->bootstrapRenderer;
        $el = $this->getControlLabel($control);
        if ($el === NULL) {
            $el = clone $r->prototypes->emptyLabel;
            if (method_exists($control, 'getHtmlId')) {
                $el->getPlaceholder()->setAttribute('for', $control->getHtmlId());
            }
        }
        if ($el instanceof Html && $r->getRenderMode() === RenderModeEnum::HORIZONTAL) {
            $el->appendAttribute('class', $r->getColumnsClass($r->getLabelColumns()));
        }
        return $el;
    }

    protected function getControlLabel(IControl $control)
    {
        return SecureCallHelper::tryCall($control, 'getLabel');
    }
}
