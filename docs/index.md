# Instante Bootstrap 3 Renderer

Suggested way is to register Bootstrap form factory as a service:

```yml
services:
    formFactory: Instante\Bootstrap3Renderer\BootstrapFormFactory
```

Inject the factory to a presenter/component/whatever and create a form

```php
class MyPresenter extends \Nette\Application\UI\Presenter
{
    /** @var \Instante\ExtendedFormMacros\IFormFactory @inject */
    public $formFactory;
    
    public function createComponentMyForm() {
        $form = $this->formFactory->create();
        // or, you may optionally pass your Form subclass:
        $form = $this->formFactory->create(MyForm::class);
        // ...
        return $form;
    }
}
```

Render the form in a template:

```smarty
{control myForm}
```

## Render modes

Bootstrap 3 supports three form display modes:

- [Basic (vertical) mode](http://getbootstrap.com/css/#forms)
- [Inline mode](http://getbootstrap.com/css/#forms-inline)
- [Horizontal mode](http://getbootstrap.com/css/#forms-horizontal)

As we find the **horizontal** mode the most natural and comfortable
 (it responsively becomes _vertical_ on mobile screens),
 it is used by Bootstrap 3 Renderer by default.

The horizontal mode uses Bootstrap grid 12 column layout to render
 label and input columns. By default, labels are rendered
 in <code>.col-sm-2</code> and inputs in <code>.col-sm-10</code>. 
 You may customize form's grid layout parameters:

```php
// BootstrapRenderer::setLabelColumns($labelColumns, $inputColumns = NULL)

// render labels over 4 grid columns and inputs over 6 columns
$form->getRenderer()->setLabelColumns(4, 6);

// if input argument is not present, it is automatically determined as 12 - labelColumns
$form->getRenderer()->setLabelColumns(4);

// if input argument is not present, it is automatically determined as 12 - labelColumns
$form->getRenderer()->setLabelColumns(4);
```

```php
// BootstrapRenderer::setColumnMinScreenSize($columnMinScreenSize)

use Instante\Bootstrap3Renderer\ScreenSizeEnum;

// horizontal on all screens
$form->getRenderer()->setColumnMinScreenSize(ScreenSizeEnum::XS);
 
// horizontal on at least 768px wide screens
$form->getRenderer()->setColumnMinScreenSize(ScreenSizeEnum::SM);
  
// horizontal on at least 992px wide screens
$form->getRenderer()->setColumnMinScreenSize(ScreenSizeEnum::MD);

// horizontal on at least 1200px wide screens
$form->getRenderer()->setColumnMinScreenSize(ScreenSizeEnum::LG);
```

You may configure the renderer to use the other two render  modes too:

```php
use Instante\Bootstrap3Renderer\RenderModeEnum;

$form->getRenderer()->setRenderMode(RenderModeEnum::HORIZONTAL);
$form->getRenderer()->setRenderMode(RenderModeEnum::INLINE);

// or reset the renderer to default
$form->getRenderer()->setRenderMode(RenderModeEnum::VERTICAL);
```

## Rendering groups

Nette renders components inside form groups first and other components
 last by default. We find this non-intuitive and unhandy because
 the way how groups are defined is exactly opposite:

```php
$form = $formFactory->create();
$form->addText('foo', 'I have no group');
$form->addGroup('A captioned group');
$form->addText('bar', 'I am grouped!');
$form->addText('baz', 'Me too!');
```

Bootstrap 3 Renderer renders the 'I have no group' input first as expected.
 If you want the default Nette form rendering behavior, use this configuration:

```php
$form->getRenderer()->setGroupsRenderedFirst();
```

## Custom form rendering

Instante Bootstrap 3 Renderer version 2 delegates advanced rendering
 features to an universal <code>instante/extended-form-macros</code>
 package. See [Extended form macros documentation](https://github.com/instante/extended-form-macros/blob/master/docs/index.md)
 to learn more.

## Custom control rendering

(**New in version 2**) custom control renderers are now supported,
handling the rendering {pair}, {label} and {input} output HTML
of specified control types.

You can configure custom renderer for a specified form control type:

```php
class FancyRenderer implements Instante\Bootstrap3Renderer\Controls\IControlRenderer {
    // ... custom implementation
}

$form->getRenderer()->controlRenderers[MyFancyTextInput::class] = new FancyRenderer;
```

See <code>Instante\Bootstrap3Renderer\Controls\DefaultControlRenderer</code>
default implementation for common controls as an example.
