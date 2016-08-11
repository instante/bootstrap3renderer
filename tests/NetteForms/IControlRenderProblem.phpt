<?php

/**
 * @outputMatch ~Call to undefined method.*setOption\(\)~
 * @exitCode 255
 */

namespace InstanteTests\Nette\Forms;

use Nette\ComponentModel\IComponent;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;
use Nette\Forms\IControl;

require '../bootstrap.php';

class FooControl implements IControl, IComponent
{
    private $parent;
    private $name;

    function setValue($value)
    {
    }

    function getValue()
    {
        return 'foo';
    }

    function validate()
    {
    }

    function getErrors()
    {
        return [];
    }

    function isOmitted()
    {
        return FALSE;
    }

    function getName()
    {
        return $this->name;
    }

    function getParent()
    {
        return $this->parent;
    }

    function setParent(IContainer $parent = NULL, $name = NULL)
    {
        $this->parent = $parent;
        $this->name = $name;
    }
}

$form = new Form;
$form->addComponent(new FooControl, 'foo');

$form->render();
