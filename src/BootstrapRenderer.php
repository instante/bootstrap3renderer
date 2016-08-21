<?php

namespace Instante\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\Utils\PlaceholderHtml;
use Instante\ExtendedFormMacros\IExtendedFormRenderer;
use Instante\Helpers\SecureCallHelper;
use Instante\Helpers\Strings;
use /** @noinspection PhpInternalEntityUsedInspection */
    Nette\Bridges\FormsLatte\Runtime;
use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\InvalidStateException;
use Nette\NotSupportedException;
use Nette\SmartObject;
use Nette\Utils\Html;
use Nette\Utils\Strings as NStrings;
use SplObjectStorage;
use Traversable;

/**
 * Created with twitter bootstrap in mind.
 *
 * <code>
 * $form->setRenderer(new Instante\Bootstrap3Renderer\BootstrapRenderer);
 * </code>
 *
 * @property PrototypeContainer $prototypes
 */
class BootstrapRenderer implements IExtendedFormRenderer
{
    use SmartObject;

    const FORM_CONTROL_CLASS = 'form-control';
    const COLUMNS_CLASS_PATTERN = 'col-%(size)-%(cols;d)';
    const COLUMNS_OFFSET_PATTERN = 'col-%(size)-offset-%(cols;d)';

    /** @var int */
    protected $labelColumns = 2;

    /** @var int */
    protected $inputColumns = 10;

    /** @var string ScreenSizeEnum */
    protected $columnMinScreenSize = ScreenSizeEnum::SM;

    /** @var bool if true, controls without group go first */
    protected $grouplessRenderedFirst = FALSE;

    /**
     * set to false, if you want to display the field errors also as form errors
     * @var bool
     */
    public $errorsAtInputs = TRUE;

    /** @var Form */
    protected $form;

    /** @var string RenderModeEnum */
    protected $renderMode = RenderModeEnum::HORIZONTAL;

    /** @var SplObjectStorage */
    private $renderedControls;

    /** @var PrototypeContainer */
    private $prototypes;

    /** @var int */
    private static $uniqueDescriptionId = 0;

    /**
     * @param null|string $renderMode RenderModeEnum
     * @param PrototypeContainer $prototypes
     */
    public function __construct($renderMode = RenderModeEnum::VERTICAL, PrototypeContainer $prototypes = NULL)
    {
        $this->renderMode = $renderMode;
        $this->prototypes = $prototypes ?: PrototypeContainer::createDefault();
    }

    /** @return PrototypeContainer */
    public function getPrototypes()
    {
        return $this->prototypes;
    }

    /**
     * @param PrototypeContainer $prototypes
     * @return $this
     */
    public function setPrototypes(PrototypeContainer $prototypes)
    {
        $this->prototypes = $prototypes;
        return $this;
    }

    /**
     * @param IControl $control
     * @return Html
     */
    public function renderPair(IControl $control)
    {
        $pair = clone $this->prototypes->pair;
        $pair->getPlaceholder('label')->addHtml($this->renderLabel($control));
        $ctrlHtml = $this->renderControl($control, TRUE);
        if ($this->renderMode === RenderModeEnum::HORIZONTAL) {
            // wrap in bootstrap columns
            $ctrlHtml = Html::el('div')
                ->appendAttribute('class', $this->getColumnsClass($this->inputColumns))
                ->addHtml($ctrlHtml);
        }
        $pair->getPlaceholder('control')->addHtml($ctrlHtml);
        $pair->getPlaceholder('errors')->addHtml($this->renderControlErrors($control));
        $pair->getPlaceholder('description')->addHtml($this->renderControlDescription($control));
        return $pair;
    }

    public function renderGroup(ControlGroup $control)
    {
        // TODO: Implement renderGroup() method.
    }

    public function renderContainer(Container $control)
    {
        // TODO: Implement renderContainer() method.
    }

    public function render(Form $form, $mode = NULL)
    {
        $s = '';
        if (!$mode || $mode === 'begin') {
            //TODO: will have to redirect {form} macro to this routine to set form class
            $s .= $this->renderBegin($form);
        }
        if (!$mode || strtolower($mode) === 'ownerrors') {
            $s .= $this->renderGlobalErrors();

        } elseif ($mode === 'errors') {
            $s .= $this->renderGlobalErrors(FALSE);
        }
        if (!$mode || $mode === 'body') {
            $s .= $this->renderBody();
        }
        if (!$mode || $mode === 'end') {
            $s .= $this->renderEnd();
        }
        return $s;
    }

    /**
     * Sets label (and optionally input) width in bootstrap columns layout
     *
     * @param int $labelColumns
     * @param int|NULL $inputColumns if null, automatically filled to 12-labelColumns
     * @return $this
     */
    public function setLabelColumns($labelColumns, $inputColumns = NULL)
    {
        if ($inputColumns === NULL) {
            $inputColumns = 12 - $labelColumns;
        }
        $this->labelColumns = $labelColumns;
        $this->inputColumns = $inputColumns;
        return $this;
    }

    /**
     * @param string $renderMode RenderModeEnum
     * @return $this
     */
    public function setRenderMode($renderMode)
    {
        RenderModeEnum::assertValidValue($renderMode);
        $this->renderMode = $renderMode;
        return $this;
    }

    /** @return string RenderModeEnum */
    public function getRenderMode()
    {
        return $this->renderMode;
    }

    public function renderBegin(Form $form, array $attrs = [])
    {
        if ($this->form !== $form) {
            $this->form = $form;
        }

        $this->renderedControls = new SplObjectStorage;
        $this->addFormModeClass($form, $attrs);
        /** @noinspection PhpInternalEntityUsedInspection */
        $rendered = Runtime::renderFormBegin($form, $attrs);
        return $rendered;
    }

    public function renderControlErrors(IControl $control)
    {
        //TODO
        return '[ERRORS]';
    }

    public function renderGlobalErrors($ownOnly = TRUE)
    {
        //TODO
        return $ownOnly ? '[OWN]' : '[GLOBAL]';
    }

    public function renderBody()
    {
        $groups = $this->renderGroups();
        $groupless = $this->renderPairs($this->form->getControls());

        return $this->areGrouplessRenderedFirst()
            ? $groupless . "\n" . $groups
            : $groups . "\n" . $groupless;
    }

    public function renderEnd()
    {
        $this->assertInForm();
        $form = $this->form;
        $this->form = NULL;
        /** @noinspection PhpInternalEntityUsedInspection */
        return Runtime::renderFormEnd($form);
    }

    private function addFormModeClass(Form $form, array &$attrs)
    {
        if ($this->renderMode !== RenderModeEnum::VERTICAL) {
            if (isset($attrs['class'])) {
                $classes = explode(' ', $attrs['class']);
                $pos = array_search('no-' . $this->renderMode, $classes, TRUE);
                if ($pos !== FALSE) {
                    // if .no-form-<mode> class is present, remove it and not include form-<mode> class
                    unset($classes[$pos]);
                } else {
                    // otherwise add form-<mode> class
                    $classes[] = $this->renderMode;
                }
                if (count($classes) === 0) {
                    unset($attrs['class']);
                } else {
                    $attrs['class'] = implode(' ', $classes);
                }
            } else {
                $classes = $form->getElementPrototype()->getAttribute('class');
                if (is_string($classes)) {
                    $classes = explode(' ', $classes);
                } elseif (!is_array($classes)) {
                    $classes = [];
                }
                $classes[] = $this->renderMode;
                $attrs['class'] = implode(' ', $classes);
            }
        }
    }

    protected function assertInForm()
    {
        if ($this->form === NULL) {
            throw new InvalidStateException('No form set, please call renderBegin($form) first.');
        }
    }

    /** @return boolean */
    public function areGrouplessRenderedFirst()
    {
        return $this->grouplessRenderedFirst;
    }

    /**
     * @param boolean $grouplessRenderedFirst
     * @return $this
     */
    public function setGrouplessRenderedFirst($grouplessRenderedFirst = TRUE)
    {
        $this->grouplessRenderedFirst = (bool)$grouplessRenderedFirst;
        return $this;
    }

    public function renderGroups()
    {
        return '[GROUPS]';
    }

    /**
     * @param IControl[]|Traversable $controls
     * @return string
     */
    protected function renderPairs($controls)
    {
        $buttons = [];
        $ret = '';
        foreach ($controls as $control) {
            if ($this->shouldRender($control)) {
                if ($this->isButton($control)) {
                    $buttons[] = $control;
                } else {
                    $ret .= $this->renderButtons($buttons);
                    $ret .= $this->renderPair($control);
                    $buttons = [];
                }
            }
        }
        $ret .= $this->renderButtons($buttons);
        return $ret;
    }

    protected function shouldRender(IControl $control)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return
            !$this->renderedControls->contains($control) // not rendered yet
            && (!$control instanceof HiddenField) // not hidden
            && (!method_exists($control, 'getForm')
                || $control->getForm() === $this->form); //belonging to this form
    }

    protected function renderButtons(array $buttons)
    {
        if (count($buttons) === 0) {
            return '';
        }
        if ($this->renderMode === RenderModeEnum::HORIZONTAL) {
            $container = clone $this->prototypes->horizontalButtons;
            // set inner grid element to "col-ss-<inputcolumns> col-ss-offset-<labelcolumns>
            $container->getPlaceholder('cols')
                ->appendAttribute('class', $this->getColumnsClass($this->inputColumns))
                ->appendAttribute('class', $this->getOffsetClass($this->labelColumns));
        } else {
            $container = PlaceholderHtml::el();
        }
        foreach ($buttons as $button) {
            $container->addHtml($this->renderButton($button));
        }
        return $container;
    }

    /**
     * @param IControl $control
     * @return PlaceholderHtml|mixed
     */
    public function renderLabel(IControl $control)
    {
        $el = SecureCallHelper::tryCall($control, 'getLabel');
        if ($el === NULL) {
            $el = clone $this->prototypes->emptyLabel;
            if (method_exists($control, 'getHtmlId')) {
                $el->getPlaceholder()->setAttribute('for', $control->getHtmlId());
            }
        }
        if ($el instanceof Html && $this->renderMode === RenderModeEnum::HORIZONTAL) {
            $el->appendAttribute('class', $this->getColumnsClass($this->labelColumns));
        }
        return $el;
    }

    /**
     * @param IControl $control
     * @param bool $renderedDescription
     * @return Html
     */
    public function renderControl(IControl $control, $renderedDescription = FALSE)
    {
        $this->renderedControls->attach($control);
        if (!method_exists($control, 'getControl')) {
            return Html::el();
        }
        /** @var Html $el */
        $el = $control->getControl();
        if ($el instanceof Html) {
            $el->appendAttribute('class', static::FORM_CONTROL_CLASS);
            if ($renderedDescription && $this->getControlDescription($control) !== NULL) {
                $el->setAttribute('aria-describedby', $this->getDescriptionId($control));
            }
        } else {
            $el = Html::el()->addHtml($el);
        }
        return $el;
    }

    protected function getColumnsClass($numberColumns)
    {
        return Strings::format(static::COLUMNS_CLASS_PATTERN, [
            'size' => $this->columnMinScreenSize,
            'cols' => $numberColumns,
        ]);
    }

    protected function getOffsetClass($numberColumns)
    {
        return Strings::format(static::COLUMNS_OFFSET_PATTERN, [
            'size' => $this->columnMinScreenSize,
            'cols' => $numberColumns,
        ]);
    }

    /** @return string ScreenSizeEnum */
    public function getColumnMinScreenSize()
    {
        return $this->columnMinScreenSize;
    }

    /**
     * @param string $columnMinScreenSize ScreenSizeEnum
     * @return $this
     */
    public function setColumnMinScreenSize($columnMinScreenSize)
    {
        ScreenSizeEnum::assertValidValue($columnMinScreenSize);
        $this->columnMinScreenSize = $columnMinScreenSize;
        return $this;
    }

    public function renderControlDescription(IControl $control)
    {
        $description = $this->getControlDescription($control);
        if ($description === NULL) {
            return Html::el();
        }
        $el = clone $this->prototypes->controlDescription;
        return $el
            ->getPlaceholder()
            ->setAttribute('id', $this->getDescriptionId($control))
            ->addHtml($description);
    }

    public function renderButton(IControl $button)
    {
        /** @var Html $el */
        $el = SecureCallHelper::tryCall($button, 'getControl');
        if ($el === NULL) {
            throw new NotSupportedException('Rendering buttons not having getControl() method is not supported');
        }
        $el->appendAttribute('class', 'btn');
        if (!$this->hasButtonTypeClass($el)) {
            if ($button instanceof SubmitButton) {
                $el->appendAttribute('class', 'btn-primary');
            } else {
                $el->appendAttribute('class', 'btn-default');
            }
        }
        return $el;
    }

    // **** intentionally private, do not rely on these as they are workarounds on IControl interface deficiencies

    private function isButton(IControl $control)
    {
        return SecureCallHelper::tryCall($control, 'getOption', 'type') === 'button';
    }

    private function getControlDescription(IControl $control)
    {
        return SecureCallHelper::tryCall($control, 'getOption', 'description');
    }

    private function getDescriptionId(IControl $control)
    {
        $id = SecureCallHelper::tryCall($control, 'getHtmlId') ?: ('-anonymous-' . (self::$uniqueDescriptionId++));
        return 'describe-' . $id;
    }

    private function hasButtonTypeClass(Html $el)
    {
        $classes = $el->getAttribute('class');
        if (is_string($classes)) {
            $classes = explode(' ', $classes);
        }
        foreach ($classes as $class) {
            if (NStrings::startsWith($class, 'btn-')) {
                return TRUE;
            }
        }
        return FALSE;
    }
}
