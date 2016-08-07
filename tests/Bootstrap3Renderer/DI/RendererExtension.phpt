<?php

namespace InstanteTests\Boostrap3Renderer\DI;

use Instante\Bootstrap3Renderer\DI\RendererExtension;

use Instante\Bootstrap3Renderer\Latte\FormMacros;
use Nette\Configurator;
use Nette\DI\Compiler;

require_once __DIR__ . '/../../bootstrap.php';

// ::register()
$mockConfigurator = mock(Configurator::class);
RendererExtension::register($mockConfigurator);
$mockCompiler = \Mockery::mock(Compiler::class);

/** @noinspection PhpMethodParametersCountMismatchInspection */
$mockCompiler->shouldReceive('addExtension')
    ->with(\Mockery::type('string'), \Mockery::type(RendererExtension::class))
    ->once();
$mockConfigurator->onCompile[0]($mockConfigurator, $mockCompiler);


// ::loadConfiguration()
$ext = new RendererExtension;
$ext->setCompiler($mockCompiler, 'foo');

$mockCompiler->shouldReceive('getContainerBuilder->getDefinition->addSetup')->withArgs(function ($arg) {
    return strpos($arg, FormMacros::class) !== FALSE;
})->once();
$ext->loadConfiguration();
