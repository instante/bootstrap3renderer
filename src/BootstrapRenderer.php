<?php

namespace Instante\Bootstrap3Renderer;

use Instante\ExtendedFormMacros\IExtendedFormRenderer;
use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\Form;
use Nette\Forms\IControl;

/**
 * Created with twitter bootstrap in mind.
 *
 * <code>
 * $form->setRenderer(new Instante\Bootstrap3Renderer\BootstrapRenderer);
 * </code>
 *
 * @author Richard Ejem
 */
class BootstrapRenderer implements IExtendedFormRenderer
{

    /** @var int */
    private $labelColumns = 2;

    /** @var int */
    private $inputColumns = 10;

    /** @var string */
    private $columnClassPrefix = 'col-sm-';

    /**
     * set to false, if you want to display the field errors also as form errors
     * @var bool
     */
    public $errorsAtInputs = TRUE;

    /** @var \Nette\Forms\Form */
    private $form;

    /** @var string RenderModeEnum */
    private $mode = RenderModeEnum::VERTICAL;

    public function renderPair(IControl $control)
    {
        // TODO: Implement renderPair() method.
    }

    public function renderGroup(ControlGroup $control)
    {
        // TODO: Implement renderGroup() method.
    }

    public function renderContainer(Container $control)
    {
        // TODO: Implement renderContainer() method.
    }

    public function render(Form $form, $mode = NULL)
    {
        $s = '';
        if (!$mode || $mode === 'begin') {
            $s .= $this->renderBegin($form);
        }
        if (!$mode || strtolower($mode) === 'ownerrors') {
            $s .= $this->renderGlobalErrors();

        } elseif ($mode === 'errors') {
            $s .= $this->renderGlobalErrors(FALSE);
        }
        if (!$mode || $mode === 'body') {
            $s .= $this->renderBody();
        }
        if (!$mode || $mode === 'end') {
            $s .= $this->renderEnd();
        }
        return $s;
    }

    /**
     * Sets label (and optionally input) width in bootstrap columns layout
     *
     * @param int $labelColumns
     * @param int|NULL $inputColumns if null, automatically filled to 12-labelColumns
     * @return $this
     */
    public function setLabelColumns($labelColumns, $inputColumns = NULL)
    {
        if ($inputColumns === NULL) {
            $inputColumns = 12 - $labelColumns;
        }
        $this->labelColumns = $labelColumns;
        $this->inputColumns = $inputColumns;
        return $this;
    }

    /**
     * @param string $mode RenderModeEnum
     * @return $this
     */
    public function setMode($mode)
    {
        RenderModeEnum::assertValidValue($mode);
        $this->mode = $mode;
        return $this;
    }

    public function renderBegin(Form $form)
    {
        if ($this->form !== $form) {
            $this->form = $form;
        }
        return '[FORM]';
    }

    private function renderControlErrors($control)
    {
        return '[END]';
    }

    private function renderGlobalErrors($ownOnly = TRUE)
    {
        return $ownOnly ? '[OWN]' : '[GLOBAL]';
    }

    private function renderBody()
    {
        return '[BODY]';
    }

    private function renderEnd()
    {
        return '[/FORM]';
    }
}
