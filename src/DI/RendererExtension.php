<?php

namespace Instante\Bootstrap3Renderer\DI;

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
        $builder->getDefinition('nette.latteFactory')
            ->addSetup("?->onCompile[] = function() use (?) { $FormMacros::install(?->getCompiler()); }",
                ['@self', '@self', '@self',]);
    }

    public static function register(Configurator $config)
    {
        $config->onCompile[] = function (Configurator $config, Compiler $compiler) {
            $compiler->addExtension('twBootstrapRenderer', new self());
        };
    }

}
