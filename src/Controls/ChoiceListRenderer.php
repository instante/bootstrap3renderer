<?php

namespace Instante\Bootstrap3Renderer\Controls;

use Instante\Bootstrap3Renderer\RenderModeEnum;
use Instante\Helpers\SecureCallHelper;
use Nette\Forms\IControl;
use Nette\Utils\Html;

abstract class ChoiceListRenderer extends DefaultControlRenderer
{
    /** @var Html item separator */
    public $separator = NULL;

    /** @var string to be overriden in descendant classes - assigns class="?[-inline]" to element wrapper */
    protected $wrapperClass;

    /** {@inheritdoc} */
    public function renderSingleChoice(IControl $control, $key)
    {
        $label = $this->renderSingleLabel($control, $key);
        $controlHtml = $this->renderSingleControl($control, $key);
        $label->insert(0, $controlHtml);
        if (($this->bootstrapRenderer->getRenderMode() === RenderModeEnum::INLINE
                && !SecureCallHelper::tryCall($control, 'getOption', 'noRenderInline'))
            || SecureCallHelper::tryCall($control, 'getOption', 'renderInline')
        ) {
            $label->appendAttribute('class', $this->wrapperClass . '-inline');
            $wrapper = $label;
        } else {
            $wrapper = Html::el('div', ['class' => $this->wrapperClass])->addHtml($label);
        }
        return $wrapper;
    }

    /**
     * div.(checkbox|radio){0..n}
     *     label
     *         input[type="checkbox|radio"]
     *         ...Label
     * ||
     * label.(checkbox|radio)-inline{0..n}
     *     input[type="checkbox|radio"]
     *     ...Label
     *
     * @param IControl $control
     * @param bool $renderedDescription
     * @return Html
     */
    public function renderControl(IControl $control, $renderedDescription = FALSE)
    {
        $el = Html::el();
        $first = TRUE;
        foreach ($this->getListItems($control) as $item) {
            if (!$first && $this->separator !== NULL) {
                $el->addHtml($this->separator);
            }
            $first = FALSE;
            $el->addHtml($this->renderSingleChoice($control, $item));
        }
        return $el;
    }

    protected abstract function getListItems(IControl $control);

    /**
     * @param IControl $control
     * @param string $key
     * @return Html
     */
    public function renderSingleLabel(IControl $control, $key)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $control->getLabelPart($key);
    }

    /**
     * @param IControl $control
     * @param string $key
     * @return Html
     */
    public function renderSingleControl(IControl $control, $key)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return $control->getControlPart($key);
    }
}
