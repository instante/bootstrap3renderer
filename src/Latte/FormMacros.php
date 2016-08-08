<?php

namespace Instante\Bootstrap3Renderer\Latte;

use Latte\CompileException;
use Latte\RuntimeException;
use Latte\Compiler;
use Latte\Macros\MacroSet;
use Latte\MacroNode;
use Latte\PhpWriter;

/**
 * Standard macros:
 * <code>
 * {form name} as {$form->render('begin')}
 * (DEPRECATED) {form errors} as {$form->render('errors')}
 * (DEPRECATED) {form body} as {$form->render('body')}
 * (DEPRECATED) {form controls} as {$form->render('controls')}
 * (DEPRECATED) {form actions} as {$form->render('actions')}
 * {/form} as {$form->render('end')}
 * </code>
 *
 * or shortcut
 *
 * <code>
 * {form name /} as {$form->render()}
 * </code>
 *
 * Old macros `input` & `label` are working the same.
 * <code>
 * {input name}
 * {label name /} or {label name}... {/label}
 * </code>
 *
 * Individual rendering:
 * <code>
 * {pair name} as {$form->render($form['name'])}
 * {group name} as {$form->render($form->getGroup('name'))}
 * {container name} as {$form->render($form['name'])}
 * </code>
 */
class FormMacros extends MacroSet
{

    /**
     * @param Compiler $compiler
     * @return MacroSet
     */
    public static function install(Compiler $compiler)
    {
        $me = new static($compiler);
        $me->addMacro('pair', [$me, 'macroPair']);
        $me->addMacro('group', [$me, 'macroGroup']);
        $me->addMacro('container', [$me, 'macroContainer']);
        return $me;
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroPair(MacroNode $node, PhpWriter $writer)
    {
        return sprintf('$this->global->formRenderingDispatcher->renderPair($this->global->formsStack, %s)',
            $this->renderFormComponent($node, $writer));
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroGroup(MacroNode $node, PhpWriter $writer)
    {
        return $writer->write('$this->global->formRenderingDispatcher->renderGroup($this->global->formsStack,'
            . 'is_object(%node.word) ? %node.word : reset($this->global->formsStack)->getGroup(%node.word))');
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroContainer(MacroNode $node, PhpWriter $writer)
    {
        return sprintf('$this->global->formRenderingDispatcher->renderContainer($this->global->formsStack, %s)',
            $this->renderFormComponent($node, $writer));
    }


    protected function renderFormComponent(MacroNode $node, PhpWriter $writer)
    {
        if ($node->modifiers) {
            throw new CompileException('Modifiers are not allowed in ' . $node->getNotation());
        }
        $words = $node->tokenizer->fetchWords();
        if (!$words) {
            throw new CompileException('Missing name in ' . $node->getNotation());
        }
        $node->replaced = TRUE;
        $name = array_shift($words);
        return $writer->write($name[0] === '$' ?
            'is_object(%0.word) ? %0.word : end($this->global->formsStack)[%0.word]' :
            'end($this->global->formsStack)[%0.word]',
            $name
        );
    }
}
