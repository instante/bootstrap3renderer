<?php

namespace Instante\Bootstrap3Renderer\DI;

use Nette\DI\Compiler;
use Nette;

if (!class_exists('Nette\DI\CompilerExtension')) {
    class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
    class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
    class_alias('Nette\Config\Helpers', 'Nette\DI\Config\Helpers');
}

if (isset(Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']) || !class_exists('Nette\Configurator')) {
    unset(Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']); // fuck you
    class_alias('Nette\Config\Configurator', 'Nette\Configurator');
}

/**
 * @author Filip Procházka <filip@prochazka.su>
 * @author Richard Ejem
 */
class RendererExtension extends Nette\DI\CompilerExtension {

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();

        $install = 'Instante\Bootstrap3Renderer\Latte\FormMacros::install';
        $builder->getDefinition('nette.latteFactory')
                ->addSetup("?->onCompile[] = function() use (?) { $install(?->getCompiler()); }", array('@self', '@self', '@self'));
    }

    /**
     * @param \Nette\Configurator $config
     */
    public static function register(Nette\Configurator $config) {
        $config->onCompile[] = function (Nette\Configurator $config, Compiler $compiler) {
            $compiler->addExtension('twBootstrapRenderer', new RendererExtension());
        };
    }

}
