<?php

namespace Instante\Bootstrap3Renderer;

use Nette\SmartObject;
use Nette\Utils\Html;

/**
 * Container for renderer HTML element prototypes
 *
 * @property Html $pair
 * @property Html $emptyLabel
 * @property Html $controlDescription
 */
class PrototypeContainer
{
    use SmartObject;

    /** @var Html */
    private $pair;

    /** @var Html */
    private $emptyLabel;

    /** @var Html */
    private $controlDescription;

    public static function createDefault()
    {
        $c = new static;
        $c->pair = Html::el('div', ['class' => 'form-group']);
        $c->emptyLabel = Html::el('label');
        $c->controlDescription = Html::el('span', ['class' => 'help-block']);
        return $c;
    }

    /** @return Html */
    public function getPair()
    {
        return $this->pair;
    }

    /**
     * @param Html $pair
     * @return $this
     */
    public function setPair(Html $pair)
    {
        $this->pair = $pair;
        return $this;
    }

    /** @return Html */
    public function getEmptyLabel()
    {
        return $this->emptyLabel;
    }

    /**
     * @param Html $emptyLabel
     * @return $this
     */
    public function setEmptyLabel(Html $emptyLabel)
    {
        $this->emptyLabel = $emptyLabel;
        return $this;
    }

    /** @return Html */
    public function getControlDescription()
    {
        return $this->controlDescription;
    }

    /**
     * @param Html $controlDescription
     * @return $this
     */
    public function setControlDescription(Html $controlDescription)
    {
        $this->controlDescription = $controlDescription;
        return $this;
    }
}
