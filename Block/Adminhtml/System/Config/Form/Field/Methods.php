<?php

namespace Magestio\PickupStore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Methods
 * @package Magestio\PickupStore\Block\Adminhtml\System\Config\Form\Field
 */
class Methods extends AbstractFieldArray
{

    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];

    /**
     * Enable the "Add after" button or not
     *
     * @var bool
     */
    protected $_addAfter = true;

    /**
     * Label of add button
     *
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * Methods constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Check if columns are defined, set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('store_name', ['label' => __('Store')]);
        $this->addColumn('store_code', ['label' => __('Code')]);
        $this->addColumn('store_price', ['label' => __('Price')]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
