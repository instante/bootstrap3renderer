#
!! THIS DOCUMENT IS OUTDATED, NEEDS REVISION FOR INSTANTE BS3 RENDERER

Instante/Bootstrap3Renderer/BootstrapRenderer

Forms Renderer for Nette Framework that allows partial rendering and uses [Twitter Bootstrap markup and classes](http://getbootstrap.com/css/#forms).
Based on Kdyby/BoostrapRenderer


## Requirements

Instante/Bootstrap3Renderer/BootstrapRenderer requires PHP 5.3.2 or higher.

- [Nette Framework 2.0.x](https://github.com/nette/nette)


## Installation

- [Get composer](http://getcomposer.org/doc/00-intro.md)
- Install package <code>kdyby/bootstrap-form-renderer</code>


## Macros

If you want to use the special macros, you have to register them into the Latte Engine:

```php
Instante\Bootstrap3Renderer\Latte\FormMacros::install($engine->compiler);
```

Or simply register the extension in `app/bootstrap.php` to allow them globally:

```php
Instante\Bootstrap3Renderer\DI\RendererExtension::register($configurator);
```


## Usage

First you have to register the renderer to the form.

```php
use Instante/Bootstrap3Renderer/BootstrapRenderer;
$form->setRenderer(new BootstrapRenderer);
```

You can provide your own template instance (for performance optimizations):

```php
// $this instanceof Nette\Application\UI\Presenter
$form->setRenderer(new BootstrapRenderer($this->createTemplate()));
```

All the usage cases expect you to have the form component in a variable named <code>$form</code>



### Basic rendering

Entire form

```smarty
{control formName} or {form formName /}
```

Beginning of the form

```smarty
{$form->render('begin')} or {form $form} or {form formName}
```

Errors

> Renders only errors that have no associated form element.

```smarty
{$form->render('errors')} or {form errors}
```

Body

> Renders all controls and groups that are not rendered yet.

```smarty
{$form->render('body')} or {form body}
```

Controls

> Renders all controls that are not rendered yet. Doesn't render buttons.

```smarty
{$form->render('controls')} or {form controls}
```

Buttons

> Renders all buttons that are not rendered yet.

```smarty
{$form->render('buttons')} or {form buttons}
```

End

> Renders all hidden inputs and a closing tag of the form.

```smarty
{$form->render('end')} or {/form}
```


### Rendering of form components

Control

> Renders the container div around the control, its label and input.

```smarty
{$form->render($form['control-name'])} or {pair control-name}
```

Container

> Renders all inputs in a container that are not rendered yet.

```smarty
{$form->render($form['container-name'])} or {container container-name}
```

Group

> Renders fieldset, legend and all controls in a group that are not rendered yet.

```smarty
{$form->render($form->getGroup('Group name'))} or {group "Group name"}
```


-----

Kdyby Framework: homepage [http://www.kdyby.org](http://www.kdyby.org) and repository [http://github.com/kdyby/framework](http://github.com/kdyby/framework).
Sandbox, pre-packaged and configured project: [http://github.com/kdyby/sandbox](http://github.com/kdyby/sandbox)
