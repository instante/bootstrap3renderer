<?php

namespace InstanteTests\Boostrap3Renderer\Latte;

use Instante\Bootstrap3Renderer\IExtendedFormRenderer;
use Instante\Bootstrap3Renderer\Latte\FormMacros;
use Instante\Bootstrap3Renderer\Latte\FormRenderingDispatcher;
use Instante\Tests\TestBootstrap;
use Latte\Engine;
use Latte\Loaders\StringLoader;
use Mockery\MockInterface;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\FormsLatte\FormMacros as NFormMacros;
use Nette\Forms\Form;

class MacroTester
{
    /** @var Form */
    private $form;

    /** @var IExtendedFormRenderer */
    private $mockRenderer;

    /** @var Template */
    private $template;

    public function __construct($templates)
    {
        $loader = new StringLoader(is_array($templates) ? $templates : ['default' => $templates]);

        $this->mockRenderer = mock(IExtendedFormRenderer::class);

        $this->form = $form = new Form;
        $form->setRenderer($this->mockRenderer);

        $this->template = $template = new Template(new Engine);
        $template->getLatte()
            ->setLoader($loader)
            ->setTempDirectory(TestBootstrap::$tempDir . '/latte')
            ->addProvider('uiControl', [
                'theForm' => $form,
            ])
            ->addProvider('formRenderingDispatcher', new FormRenderingDispatcher);
        NFormMacros::install($template->getLatte()->getCompiler());
        FormMacros::install($template->getLatte()->getCompiler());

    }

    /** @return Form */
    public function getForm()
    {
        return $this->form;
    }

    /** @return IExtendedFormRenderer|MockInterface */
    public function getMockRenderer()
    {
        return $this->mockRenderer;
    }

    public function render($template = 'default')
    {
        ob_start();
        $this->template->render($template);
        return ob_get_clean();
    }
}
