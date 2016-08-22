<?php

namespace Instante\Bootstrap3Renderer\Controls;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\ExtendedFormMacros\IControlRenderer;
use Instante\Helpers\SecureCallHelper;
use Nette\Forms\IControl;
use Nette\Utils\Html;

class DefaultControlRenderer implements IControlRenderer
{
    /** @var BootstrapRenderer */
    private $bootstrapRenderer;

    public function __construct(BootstrapRenderer $bootstrapRenderer)
    {
        $this->bootstrapRenderer = $bootstrapRenderer;
    }

    /**
     * @param IControl $control
     * @return Html
     */
    public function renderPair(IControl $control)
    {
        $r = $this->bootstrapRenderer;

        $pair = clone $r->getPrototypes()->pair;
        $pair->getPlaceholder('label')->addHtml($r->renderLabel($control));
        $ctrlHtml = $r->renderControl($control, TRUE);
        if ($r->getRenderMode() === RenderModeEnum::HORIZONTAL) {
            // wrap in bootstrap columns
            $ctrlHtml = Html::el('div')
                ->appendAttribute('class', $r->getColumnsClass($r->getInputColumns()))
                ->addHtml($ctrlHtml);
        }
        $pair->getPlaceholder('control')->addHtml($ctrlHtml);
        $pair->getPlaceholder('errors')->addHtml($r->renderControlErrors($control));
        $pair->getPlaceholder('description')->addHtml($r->renderControlDescription($control));
        return $pair;
    }

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

    /**
     * @param IControl $control
     * @return Html
     */
    public function renderLabel(IControl $control)
    {
        $r = $this->bootstrapRenderer;
        $el = SecureCallHelper::tryCall($control, 'getLabel');
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
}
