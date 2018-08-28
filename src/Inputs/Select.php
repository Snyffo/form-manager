<?php
declare(strict_types = 1);

namespace FormManager\Inputs;

use FormManager\Node;
use FormManager\InputInterface;

/**
 * Class representing a HTML textarea element
 */
class Select extends Input
{
    private $allowNewValues = false;
    private $options = [];

    public function __construct(array $options, string $label = null, array $attributes = [])
    {
        parent::__construct('select', $attributes);

        foreach ($options as $value => $text) {
            if (is_array($text)) {
                $this->addOptgroup($value, $text);
                continue;
            }

            $this->addOption($value, (string) $text);
        }

        if (isset($label)) {
            $this->setLabel($label);
        }
    }

    public function allowNewValues(bool $allowNewValues = true): self
    {
        $this->allowNewValues = $allowNewValues;

        return $this;
    }

    public function setValue($value): InputInterface
    {
        if ($this->allowNewValues) {
            $this->addNewValues((array) $value);
        }

        if ($this->getAttribute('multiple')) {
            $this->setMultipleValues((array) $value);
            return $this;
        }

        foreach ($this->options as $option) {
            $option->selected = (string) $option->value === (string) $value;
        }

        return $this;
    }

    public function getValue()
    {
        $values = [];

        foreach ($this->options as $option) {
            if ($option->getAttribute('selected')) {
                $values[] = $option->getAttribute('value');
            }
        }

        if ($this->getAttribute('multiple')) {
            return $values;
        }

        return $values[0] ?? null;
    }

    protected function setMultipleValues(array $values)
    {
        $values = array_map(
            function ($value) {
                return (string) $value;
            },
            $values
        );

        foreach ($this->options as $option) {
            $option->selected = in_array((string) $option->value, $values, true);
        }
    }

    protected function addNewValues(array $values)
    {
        foreach ($values as $value) {
            foreach ($this->options as $option) {
                if ((string) $option->value === (string) $value) {
                    continue 2;
                }
            }

            $this->addOption($value);
        }
    }

    private function addOptgroup($label, array $options)
    {
        $optgroup = new Node('optgroup', compact('label'));

        foreach ($options as $value => $label) {
            $this->addOption($value, $label, $optgroup);
        }

        $this->appendChild($optgroup);
    }

    private function addOption($value, string $label = null, Node $parent = null)
    {
        $option = new Node('option', compact('value'));
        $option->innerHTML = $label ?: (string) $value;

        $this->options[] = $option;

        if ($parent) {
            $parent->appendChild($option);
        } else {
            $this->appendChild($option);
        }
    }
}