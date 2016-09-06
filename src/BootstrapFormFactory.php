<?php

namespace Instante\Bootstrap3Renderer;

use Instante\ExtendedFormMacros\IFormFactory;
use Nette\Forms\Form;
use Nette\Application\UI\Form as UIForm;
use Nette\SmartObject;

class BootstrapFormFactory implements IFormFactory
{
    use SmartObject;

    /**
     * @param string $formClass - Nette\Forms\Form descendant class. If NULL, it uses
     * if Nette\Application\UI\Form if it exists or Nette\Forms\Form otherwise.
     * @return Form|IBootstrapRenderedForm
     */
    public function create($formClass = NULL)
    {
        if ($formClass === NULL) {
            $formClass = class_exists(UIForm::class) ? UIForm::class : Form::class;
        }
        /** @var Form $form */
        $form = new $formClass;
        $form->setRenderer(new BootstrapRenderer);
        return $form;
    }

}
