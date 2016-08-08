<?php

namespace Instante\Bootstrap3Renderer\DI;

use Instante\Bootstrap3Renderer\Latte\FormRenderingDispatcher;
use Instante\Bootstrap3Renderer\Latte\FormMacros;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

/**
 * @author Filip Procházka <filip@prochazka.su>
 * @author Richard Ejem
 */
class RendererExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $FormMacros = FormMacros::class;
        $builder->addDefinition($this->prefix('formRenderingDispatcher'))
            ->setClass(FormRenderingDispatcher::class);
        $builder->getDefinition('latte.latteFactory')
            ->addSetup("?->onCompile[] = function() use (?) { $FormMacros::install(?->getCompiler()); }",
                ['@self', '@self', '@self',])
            ->addSetup("?->addProvider('formRenderingDispatcher', ?)", ['@self', $this->prefix('@formRenderingDispatcher'),]);
    }

    public static function register(Configurator $config)
    {
        $config->onCompile[] = function (Configurator $config, Compiler $compiler) {
            $compiler->addExtension('twBootstrapRenderer', new self());
        };
    }

}
