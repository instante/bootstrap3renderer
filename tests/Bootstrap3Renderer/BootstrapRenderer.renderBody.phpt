<?php

namespace InstanteTests\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Forms\Form;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

class BodyTestingBootstrapRenderer extends BootstrapRenderer
{
    public function renderGroups()
    {
        return '<GROUPS>';
    }

    protected function renderPairs($controls)
    {
        return '<PAIRS>';
    }
}

$form = new Form;

$renderer = new BodyTestingBootstrapRenderer;
$renderer->renderBegin($form); //to fetch form object

Assert::match('~<GROUPS>\s*<PAIRS>~', $renderer->renderBody());
$renderer->setGrouplessRenderedFirst();
Assert::match('~<PAIRS>\s*<GROUPS>~', $renderer->renderBody());
$renderer->setGrouplessRenderedFirst(FALSE);
Assert::match('~<GROUPS>\s*<PAIRS>~', $renderer->renderBody());
