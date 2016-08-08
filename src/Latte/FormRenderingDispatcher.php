<?php

namespace Instante\Bootstrap3Renderer\Latte;

use Instante\Bootstrap3Renderer\IExtendedFormRenderer;
use Latte\RuntimeException;
use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\IControl;

class FormRenderingDispatcher
{
    public function renderPair(array $formsStack, IControl $control)
    {
        $this->checkInsideForm($formsStack, 'pair');
        $this->getExtendedRenderer($formsStack, 'pair')->renderPair($control);
    }

    public function renderGroup(array $formsStack, ControlGroup $group)
    {
        $this->checkInsideForm($formsStack, 'group')->checkInsideTopLevelForm($formsStack, 'group');
        $this->getExtendedRenderer($formsStack, 'group')->renderGroup($group);
    }

    public function renderContainer(array $formsStack, Container $container)
    {
        $this->checkInsideForm($formsStack, 'container');
        $this->getExtendedRenderer($formsStack, 'container')->renderContainer($container);
    }

    protected function checkInsideTopLevelForm($formsStack, $macro)
    {
        if (count($formsStack) > 1) {
            throw new RuntimeException(sprintf('Macro %s must not be used in nested form container', $macro));
        }
        return $this;
    }

    protected function checkInsideForm($formsStack, $macro)
    {
        if (count($formsStack) === 0) {
            throw new RuntimeException(sprintf('Cannot use %s macro outside form', $macro));
        }
        return $this;
    }

    /**
     * @param array $formsStack
     * @param string $macro
     * @return IExtendedFormRenderer
     * @throws RuntimeException
     */
    protected function getExtendedRenderer(array $formsStack, $macro)
    {
        $renderer = reset($formsStack)->getRenderer();
        if (!$renderer instanceof IExtendedFormRenderer) {
            throw new RuntimeException(sprintf('%s does not support {%s} macro, please use %s as form renderer',
                get_class($renderer),
                $macro,
                IExtendedFormRenderer::class
            ));
        }
        return $renderer;
    }
}
