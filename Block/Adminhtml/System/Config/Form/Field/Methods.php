<?php

namespace Magestio\PickupStore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magestio\PickupStore\Model\Config\Source\Exclusion;
/**
 * Class Methods
 * @package Magestio\PickupStore\Block\Adminhtml\System\Config\Form\Field
 */
class Methods extends AbstractFieldArray
{

    /**
     * @var Exclusion
     */
    private $exclusion;
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
        $this->addColumn('negate', ['label' => __('¿Exclude postal codes listed below?'),'renderer' => $this->getNegateRenderer()]);
        $this->addColumn('specificpostcode', ['label' => __('List of Post Codes that can (o can\'t) use Pickup Store')]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $tax = $row->getNegate();
        if ($tax !== null) {
            $options['option_' . $this->getNegateRenderer()->calcOptionHash($tax)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
    private function getNegateRenderer()
    {
        if (!$this->exclusion) {
            $this->exclusion = $this->getLayout()->createBlock(
                Exclusion::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->exclusion;
    }
}
