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

    /**
     * Removes Html element anywhere from descendant structure
     *
     * @param Html $el
     * @param bool $all search for and remove all occurrences
     */
    public function removeDescendant(Html $el, $all = FALSE)
    {
        self::_removeDescendant($el, $this, $all);
    }

    /**
     * Removes the placeholder html element from descendant structure
     *
     * @param string $name
     * @param bool $all search for and remove all occurrences
     */
    public function removePlaceholder($name, $all = FALSE)
    {
        $this->removeDescendant($this->getPlaceholder($name), $all);
    }

    private static function _removeDescendant(Html $el, Html $target, $all)
    {
        foreach ($target->getChildren() as $index => $child) {
            if ($child instanceof Html) {
                if ($child === $el) {
                    if ($all) {
                        unset($target->children[$index]);
                        $unset = TRUE;
                    } else {
                        unset($target[$index]);
                        return TRUE;
                    }
                } else {
                    if (self::_removeDescendant($el, $child, $all) && !$all) {
                        return TRUE;
                    }
                }
            }
        }
        if (isset($unset)) {
            $target->children = array_values($target->children);
        }
        return FALSE;
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
