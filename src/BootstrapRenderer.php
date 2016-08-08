<?php

namespace Instante\Bootstrap3Renderer;

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
        if ($this->form !== $form) {
            $this->form = $form;
        }

        $s = '';
        if (!$mode || $mode === 'begin') {
            $s .= $this->renderBegin();
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
     * @param string $mode RenderModeEnum
     * @return $this
     */
    public function setMode($mode)
    {
        RenderModeEnum::assertValidValue($mode);
        $this->mode = $mode;
        return $this;
    }

    private function renderBegin()
    {
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
