<?php

namespace Instante\Bootstrap3Renderer\Utils;

use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\Utils\Html;

class PlaceholderHtml extends Html
{
    const DEFAULT_PLACEHOLDER = '__default';

    /** @var Html[] */
    protected $placeholders = [];

    private $originalMe;

    public function __construct()
    {
        $this->originalMe = $this;
        $this->setPlaceholder();
    }


    public function setPlaceholder($placeholder = NULL, $name = self::DEFAULT_PLACEHOLDER)
    {
        if ($placeholder === NULL) {
            $this->placeholders[$name] = $this;
        } elseif ($placeholder instanceof Html) {
            $this->placeholders[$name] = $placeholder;
        } elseif (is_string($placeholder) && func_num_args() === 1) {
            $this->placeholders[$placeholder] = $this;
        } else {
            throw new InvalidArgumentException('Illegal arguments, use ' . __METHOD__ . '([Html $el], [string name])');
        }
        return $this;
    }

    public function getPlaceholder($name = self::DEFAULT_PLACEHOLDER)
    {
        if (isset($this->placeholders[$name])) {
            return $this->placeholders[$name];
        } else {
            throw new InvalidStateException(sprintf('Trying to get non-existing placeholder %s, existing placeholders: %s',
                $name, implode(',', array_keys($this->placeholders))
            ));
        }
    }

    public function __clone()
    {
        parent::__clone();
        $this->clonePlaceholder($this->originalMe, $this);
        $this->cloneChildrenWithPlaceholders($this, $this->originalMe->children);
        $this->originalMe = $this;
    }


    private function cloneChildrenWithPlaceholders(Html $el, array $origChildren)
    {
        foreach ($el->children as $key => $value) {
            if ($value instanceof Html) {
                $this->cloneChildrenWithPlaceholders($value, $origChildren[$key]->children);
                $this->clonePlaceholder($origChildren[$key], $value);
            }
        }
    }

    private function clonePlaceholder(Html $origin, Html $clone)
    {
        foreach ($this->placeholders as $key => $placeholder) {
            if ($placeholder === $origin) {
                $this->placeholders[$key] = $clone;
            }
        }
    }
}
