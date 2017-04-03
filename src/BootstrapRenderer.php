<?php

namespace Instante\Bootstrap3Renderer;

use Instante\Bootstrap3Renderer\Controls\CheckboxRenderer;
use Instante\Bootstrap3Renderer\Controls\ChoiceListRenderer;
use Instante\Bootstrap3Renderer\Controls\DefaultControlRenderer;
use Instante\Bootstrap3Renderer\Controls\TextBaseRenderer;
use Instante\Bootstrap3Renderer\Utils\PlaceholderHtml;
use Instante\Bootstrap3Renderer\Controls\IControlRenderer;
use Instante\ExtendedFormMacros\IExtendedFormRenderer;
use Instante\ExtendedFormMacros\PairAttributes;
use Instante\Helpers\SecureCallHelper;
use Instante\Helpers\Strings;
use /** @noinspection PhpInternalEntityUsedInspection */
    Nette\Bridges\FormsLatte\Runtime;
use Nette\Forms\Container;
use Nette\Forms\ControlGroup;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextBase;
use Nette\Forms\Form;
use Nette\Forms\IControl;
use Nette\InvalidStateException;
use Nette\NotSupportedException;
use Nette\SmartObject;
use Nette\Utils\Html;
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

    const COLUMNS_CLASS_PATTERN = 'col-%(size)-%(cols;d)';
    const COLUMNS_OFFSET_PATTERN = 'col-%(size)-offset-%(cols;d)';

    /** @var int */
    protected $labelColumns = 2;

    /** @var int */
    protected $inputColumns = 10;

    /** @var string ScreenSizeEnum */
    protected $columnMinScreenSize = ScreenSizeEnum::SM;

    /** @var bool if true, controls without group go first */
    protected $groupsRenderedFirst = FALSE;

    /**
     * set to false, if you want to display the field errors also as form errors
     * @var bool
     */
    public $errorsAtInputs = TRUE;

    public $controlRenderers = [];

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
        $checkboxListRenderer = new ChoiceListRenderer($this, 'checkbox');
        $radioListRenderer = new ChoiceListRenderer($this, 'radio');
        $checkboxRenderer = new CheckboxRenderer($this);
        $textBaseRenderer = new TextBaseRenderer($this);
        $this->controlRenderers = [
            CheckboxList::class => $checkboxListRenderer,
            'Nextras\Forms\Controls\MultiOptionList' => $checkboxListRenderer,
            'Kdyby\Forms\Controls\CheckboxList' => $checkboxListRenderer,

            RadioList::class => $radioListRenderer,
            Checkbox::class => $checkboxRenderer,

            TextBase::class => $textBaseRenderer,
            SelectBox::class => $textBaseRenderer,

            '*' => new DefaultControlRenderer($this),
        ];
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
     * @param PairAttributes $attrs
     * @return Html
     */
    public function renderPair(IControl $control, PairAttributes $attrs = NULL)
    {
        $this->assertInForm();
        $this->renderedControls->attach($control);

        $pair = $this->getControlRenderer($control)->renderPair($control, $attrs);
        if (count($control->getErrors()) > 0) {
            $pair->appendAttribute('class', 'has-error');
        }
        if (SecureCallHelper::tryCall($control, 'isRequired')) {
            $pair->appendAttribute('class', 'required');
        }
        return $pair;
    }

    public function renderGroup(ControlGroup $group)
    {
        $this->assertInForm();

        $el = clone $this->prototypes->getControlGroup();

        //group label
        $label = $group->getOption('label');
        if ($label) {
            $this->addContent($el->getPlaceholder('label'), $label);
        } else {
            $el->removePlaceholder('label');
        }

        // group description
        $description = $group->getOption('description');
        if ($description) {
            $this->addContent($el->getPlaceholder('description'), $description);
        } else {
            $el->removePlaceholder('description');
        }

        // master element attributes
        $groupAttrs = $group->getOption('container', Html::el())->setName(NULL);
        $el->addAttributes($groupAttrs->attrs);

        // group content
        $el->addHtml($this->renderPairs($group->getControls()));

        return $el;
    }

    public function renderContainer(Container $container)
    {
        $this->assertInForm();

        return $this->renderPairs($container->getControls());
    }

    /** {@inheritdoc} */
    public function render(Form $form)
    {
        $s = $this->renderBegin($form);
        $s .= $this->renderGlobalErrors();
        $s .= $this->renderBody();
        $s .= $this->renderEnd();
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

    public function renderBegin(Form $form, array $attrs = [], $withTags = TRUE)
    {
        if ($this->form !== $form) {
            $this->form = $form;
        }

        $this->renderedControls = new SplObjectStorage;
        $this->addFormModeClass($form, $attrs);
        /** @noinspection PhpInternalEntityUsedInspection */
        $rendered = Runtime::renderFormBegin($form, $attrs, $withTags);
        return $rendered;
    }

    public function renderControlErrors(IControl $control)
    {
        $container = clone $this->prototypes->getControlErrors();
        foreach ($control->getErrors() as $error) {
            $el = clone $this->prototypes->getControlError();
            $this->addContent($el->getPlaceholder(), $error);
            $container->getPlaceholder()->addHtml($el);
        }
        return $container;
    }

    /**
     * @param bool $ownOnly - true = render only global errors, false = render all errors of all controls
     * @return Html
     */
    public function renderGlobalErrors($ownOnly = TRUE)
    {
        $errors = $ownOnly ? $this->form->getOwnErrors() : $this->form->getErrors();
        $container = clone $this->prototypes->getGlobalErrors();
        foreach ($errors as $error) {
            $alert = clone $this->prototypes->getGlobalError();
            $this->addContent($alert->getPlaceholder(), $error);
            $container->getPlaceholder()->addHtml($alert);
        }
        return $container;
    }

    public function renderBody()
    {
        $this->assertInForm();

        $groups = $this->renderGroups();
        $groupless = $this->renderPairs($this->form->getControls());

        return $this->areGroupsRenderedFirst()
            ? $groups . "\n" . $groupless
            : $groupless . "\n" . $groups;
    }

    public function renderEnd($withTags = TRUE)
    {
        $this->assertInForm();
        $form = $this->form;
        $this->form = NULL;
        /** @noinspection PhpInternalEntityUsedInspection */
        return Runtime::renderFormEnd($form, $withTags);
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
    public function areGroupsRenderedFirst()
    {
        return $this->groupsRenderedFirst;
    }

    /**
     * @param boolean $groupsRenderedFirst
     * @return $this
     */
    public function setGroupsRenderedFirst($groupsRenderedFirst = TRUE)
    {
        $this->groupsRenderedFirst = (bool)$groupsRenderedFirst;
        return $this;
    }

    /**
     * @param ControlGroup[] $groups
     * @return Html
     */
    public function renderGroups(array $groups = NULL)
    {
        if ($groups === NULL) {
            $groups = $this->getGroupsToRender();
        }
        $result = Html::el();
        foreach ($groups as $group) {
            $result->addHtml($this->renderGroup($group));
        }
        return $result;
    }


    /**
     * @param IControl[]|Traversable $controls
     * @return Html
     */
    protected function renderPairs($controls)
    {
        $buttons = [];
        $ret = Html::el();
        foreach ($controls as $control) {
            if ($this->shouldRender($control)) {
                if ($this->isButton($control)) {
                    $buttons[] = $control;
                } else {
                    if (count($buttons) > 0) {
                        $ret->addHtml($this->renderButtons($buttons));
                        $this->addSpace($ret);
                    }
                    $ret->addHtml($this->renderPair($control));
                    $this->addSpace($ret);
                    $buttons = [];
                }
            }
        }
        if (count($buttons) > 0) {
            $ret->addHtml($this->renderButtons($buttons));
        }
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

    /**
     * @param IControl[] $buttons
     * @return Html
     */
    public function renderButtons(array $buttons)
    {
        if (count($buttons) === 0) {
            return Html::el();
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
        $first = TRUE;
        foreach ($buttons as $button) {
            if ($first) {
                $first = FALSE;
            } else {
                $this->addSpace($container);
            }
            $container->addHtml($this->renderButton($button));

        }
        return $container;
    }

    /**
     * @param IControl $control
     * @param array $attrs
     * @param string $part
     * @return PlaceholderHtml|mixed
     */
    public function renderLabel(IControl $control, array $attrs = [], $part = NULL)
    {
        return $this->getControlRenderer($control)->renderLabel($control, $attrs, $part);
    }

    /**
     * @param IControl $control
     * @param array $attrs
     * @param string $part
     * @param bool $renderedDescription
     * @return Html
     */
    public function renderControl(IControl $control, array $attrs = [], $part = NULL, $renderedDescription = FALSE)
    {
        $this->assertInForm();
        $this->renderedControls->attach($control);

        $controlRenderer = $this->getControlRenderer($control);
        if ($this->isButton($control) && $controlRenderer === $this->controlRenderers['*']) {
            return $this->renderButton($control, $attrs);
        } else {
            return $controlRenderer->renderControl($control, $attrs, $part, $renderedDescription);
        }
    }

    /**
     * @param int $numberColumns
     * @return string
     */
    public function getColumnsClass($numberColumns)
    {
        return Strings::format(static::COLUMNS_CLASS_PATTERN, [
            'size' => $this->columnMinScreenSize,
            'cols' => $numberColumns,
        ]);
    }

    /**
     * @param int $numberColumns
     * @return string
     */
    public function getOffsetClass($numberColumns)
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
        $elDescription = $el->getPlaceholder();
        $elDescription->setAttribute('id', $this->getDescriptionId($control));
        $this->addContent($elDescription, $description);
        return $el;
    }

    public function renderButton(IControl $button, array $attrs = [])
    {
        $this->assertInForm();
        $this->renderedControls->attach($button);

        /** @var Html $el */
        $el = SecureCallHelper::tryCall($button, 'getControl');
        if ($el === NULL) {
            throw new NotSupportedException('Rendering buttons not having getControl() method is not supported');
        }
        $el->appendAttribute('class', 'btn');
        $el->addAttributes($attrs);
        if (!$this->hasButtonTypeClass($el)) {
            if ($button instanceof SubmitButton) {
                $el->appendAttribute('class', 'btn-primary');
            } else {
                $el->appendAttribute('class', 'btn-default');
            }
        }
        return $el;
    }

    /** @return int */
    public function getLabelColumns()
    {
        return $this->labelColumns;
    }

    /** @return int */
    public function getInputColumns()
    {
        return $this->inputColumns;
    }

    /**
     * @param IControl $control
     * @return \Instante\Bootstrap3Renderer\Controls\IControlRenderer
     */
    protected function getControlRenderer(IControl $control)
    {
        $renderer = SecureCallHelper::tryCall($control, 'getOption', 'renderer');
        if ($renderer instanceof IControlRenderer) {
            return $renderer;
        }

        foreach ($this->controlRenderers as $key => $val) {
            if ($key === '*') {
                continue;
            }
            if ($control instanceof $key) {
                SecureCallHelper::tryCall($control, 'setOption', 'renderer', $renderer); //try to cache the renderer to the control
                return $val;
            }
        }
        $renderer = $this->controlRenderers['*'];
        SecureCallHelper::tryCall($control, 'setOption', 'renderer', $renderer);
        return $renderer;
    }

    public function getControlDescription(IControl $control)
    {
        return SecureCallHelper::tryCall($control, 'getOption', 'description');
    }

    public function getDescriptionId(IControl $control)
    {
        $id = SecureCallHelper::tryCall($control, 'getHtmlId') ?: ('-anonymous-' . (self::$uniqueDescriptionId++));
        return 'describe-' . $id;
    }

    /**
     * @param Html $el
     * @param Html|string $content
     * @return Html fluent interface
     */
    private function addContent(Html $el, $content)
    {
        if ($content instanceof Html) {
            $el->addHtml($content);
        } else {
            $el->addText($content);
        }
        return $el;
    }

    // **** intentionally private, do not rely on these as they are workarounds on IControl interface deficiencies

    private function isButton(IControl $control)
    {
        return SecureCallHelper::tryCall($control, 'getOption', 'type') === 'button';
    }

    private function hasButtonTypeClass(Html $el)
    {
        $rendered = $el->startTag();
        return preg_match('~class="[^"]*\bbtn-[a-z]+\b~', $rendered);
    }

    /**
     * Determines which groups should be rendered (visual groups with at least one control).
     *
     * @return ControlGroup[]
     */
    private function getGroupsToRender()
    {
        $groups = array_filter($this->form->getGroups(), function (ControlGroup $group) {
            return $group->getControls() && $group->getOption('visual');
        });
        return $groups;
    }

    private function addSpace(Html $ret)
    {
        $ret->addText("\n");
    }
}
