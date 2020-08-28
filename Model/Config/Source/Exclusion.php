<?php

namespace Magestio\PickupStore\Model\Config\Source;


use Magento\Framework\View\Element\Html\Select;


class Exclusion extends Select
{
    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    private function getSourceOptions(): array
    {
        return [
            ['value' => 1, 'label' => __('Exclude')],
            ['value' => 0, 'label' => __('Include')]
        ];
    }
    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }
}