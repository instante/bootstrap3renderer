<?php

namespace Instante\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\Utils\PlaceholderHtml;
use Nette\SmartObject;
use Nette\Utils\Html;

/**
 * Container for renderer Html element prototypes
 *
 * @property PlaceholderHtml $pair
 * @property PlaceholderHtml $emptyLabel
 * @property PlaceholderHtml $controlDescription
 * @property PlaceholderHtml $horizontalButtons
 * @property PlaceholderHtml $globalError
 * @property PlaceholderHtml $globalErrors
 * @property PlaceholderHtml $controlError
 * @property PlaceholderHtml $controlErrors
 * @property PlaceholderHtml $controlGroup
 */
class PrototypeContainer
{
    use SmartObject;

    /** @var PlaceholderHtml */
    private $pair;

    /** @var PlaceholderHtml */
    private $emptyLabel;

    /** @var PlaceholderHtml */
    private $controlDescription;

    /** @var PlaceholderHtml */
    private $horizontalButtons;

    /** @var PlaceholderHtml */
    private $globalError;

    /** @var PlaceholderHtml */
    private $globalErrors;

    /** @var PlaceholderHtml */
    private $controlError;

    /** @var PlaceholderHtml */
    private $controlErrors;

    /** @var PlaceholderHtml */
    private $controlGroup;

    public static function createDefault()
    {
        $c = new static;
        $c->pair = PlaceholderHtml::el('div', ['class' => 'form-group']);
        $labelPlaceholder = Html::el();
        $inputPlaceholder = Html::el();
        $c->pair->addHtml($labelPlaceholder);
        $c->pair->addText("\n");
        $c->pair->addHtml($inputPlaceholder);
        $c->pair
            ->setPlaceholder($labelPlaceholder, 'label')
            ->setPlaceholder($inputPlaceholder, 'control')
            ->setPlaceholder($inputPlaceholder, 'errors')
            ->setPlaceholder($inputPlaceholder, 'description');
        $c->emptyLabel = PlaceholderHtml::el('label');
        $c->controlDescription = PlaceholderHtml::el('span', ['class' => 'help-block']);

        $buttonsInner = Html::el('div');
        $c->horizontalButtons = PlaceholderHtml::el('div', ['class' => 'form-group'])
            ->addHtml($buttonsInner)
            ->setPlaceholder($buttonsInner)
            ->setPlaceholder($buttonsInner, 'cols');
        $c->globalErrors = PlaceholderHtml::el();
        $c->globalError = PlaceholderHtml::el('div', [
            'class' => 'alert alert-warning alert-dismissible',
            'role' => 'alert',
        ])->addHtml('<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            . '<span aria-hidden="true">&times;</span></button>');
        $c->controlError = PlaceholderHtml::el('span', ['class' => 'help-block text-danger']);
        $c->controlErrors = PlaceholderHtml::el();

        $label = Html::el('legend');
        $description = Html::el('p');
        $c->controlGroup = PlaceholderHtml::el('fieldset')
            ->addHtml($label)
            ->addHtml($description)
            ->setPlaceholder($label, 'label')
            ->setPlaceholder($description, 'description');

        return $c;
    }

    /** @return PlaceholderHtml */
    public function getPair()
    {
        return $this->pair;
    }

    /**
     * @param PlaceholderHtml $pair
     * @return $this
     */
    public function setPair(PlaceholderHtml $pair)
    {
        $this->pair = $pair;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getEmptyLabel()
    {
        return $this->emptyLabel;
    }

    /**
     * @param PlaceholderHtml $emptyLabel
     * @return $this
     */
    public function setEmptyLabel(PlaceholderHtml $emptyLabel)
    {
        $this->emptyLabel = $emptyLabel;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getControlDescription()
    {
        return $this->controlDescription;
    }

    /**
     * @param PlaceholderHtml $controlDescription
     * @return $this
     */
    public function setControlDescription(PlaceholderHtml $controlDescription)
    {
        $this->controlDescription = $controlDescription;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getHorizontalButtons()
    {
        return $this->horizontalButtons;
    }

    /**
     * @param PlaceholderHtml $horizontalButtons
     * @return $this
     */
    public function setHorizontalButtons(PlaceholderHtml $horizontalButtons)
    {
        $this->horizontalButtons = $horizontalButtons;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getGlobalError()
    {
        return $this->globalError;
    }

    /**
     * @param PlaceholderHtml $globalError
     * @return $this
     */
    public function setGlobalError(PlaceholderHtml $globalError)
    {
        $this->globalError = $globalError;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getGlobalErrors()
    {
        return $this->globalErrors;
    }

    /**
     * @param PlaceholderHtml $globalErrors
     * @return $this
     */
    public function setGlobalErrors(PlaceholderHtml $globalErrors)
    {
        $this->globalErrors = $globalErrors;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getControlError()
    {
        return $this->controlError;
    }

    /**
     * @param PlaceholderHtml $controlError
     * @return $this
     */
    public function setControlError(PlaceholderHtml $controlError)
    {
        $this->controlError = $controlError;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getControlErrors()
    {
        return $this->controlErrors;
    }

    /**
     * @param PlaceholderHtml $controlErrors
     * @return $this
     */
    public function setControlErrors(PlaceholderHtml $controlErrors)
    {
        $this->controlErrors = $controlErrors;
        return $this;
    }

    /** @return PlaceholderHtml */
    public function getControlGroup()
    {
        return $this->controlGroup;
    }

    /**
     * @param PlaceholderHtml $controlGroup
     * @return $this
     */
    public function setControlGroup(PlaceholderHtml $controlGroup)
    {
        $this->controlGroup = $controlGroup;
        return $this;
    }
}
