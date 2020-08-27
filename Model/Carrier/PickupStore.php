<?php

namespace Magestio\PickupStore\Model\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;

/**
 * Class PickupStore
 * @package Magestio\PickupStore\Model\Carrier
 */
class PickupStore extends AbstractCarrier implements CarrierInterface
{

    const CARRIER_CODE = 'magestiopickupstore';

    const POST_CODE_CONFIG = 'specificpostcode';
    const NEGATE_CONFIG = 'negate';
    /**
     * @var string
     */
    protected $_code = self::CARRIER_CODE;

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var MethodFactory
     */
    protected $_session;
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param JsonSerializer $jsonSerializer
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        JsonSerializer $jsonSerializer,
        Session $session,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->_session = $session;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $postCodeConfig = $this->getConfigData(self::POST_CODE_CONFIG);
        $negate = $this->getConfigData(self::NEGATE_CONFIG);

        $shippingPostCode = $this->_session->getQuote()->getShippingAddress()->getPostcode();

        $val_true = true;
        $val_false = false;
        if( !isset($shippingPostCode) || $shippingPostCode === "")
            return false;

        if(isset($postCodeConfig)){
            $available = $val_false;

            $postCodeConfig = trim($postCodeConfig);
            if(isset($negate) && $negate == 1){
                $val_true = false;
                $val_false = true;
                $available = $val_false;
            }
            if($postCodeConfig === "*")
                $available = $val_true;
            else{
                if( strpos($postCodeConfig, ';') !== false ) {
                    $postCodeConfig = explode(";", $postCodeConfig);
                }else {
                    $postCodeConfig = array($postCodeConfig);
                }
                foreach($postCodeConfig as $pc){
                    if(strpos($pc, '*')){
                        //Wildcard post code
                        $pc = str_replace('*','.',$pc);
                        if(preg_match("/$pc/",$shippingPostCode))
                            $available = $val_true;
                    }else{
                        // Alone postcode
                        if(strpos($pc, '-') == false){
                            if($pc === $shippingPostCode)
                                $available = $val_true;
                            // Postcode in range;
                        }else{
                            $pc = explode("-",$pc);
                            if($shippingPostCode >= $pc[0] && $shippingPostCode <= $pc[1])
                                $available = $val_true;
                        }
                    }
                }
            }

            if($available == false)
                return false;
        }

        $methods = $this->jsonSerializer->unserialize($this->getConfigData('methods'));
        $name = $this->getConfigData('name') !== null ? $this->getConfigData('name') : __("Store pickup");

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        foreach ($methods as $method) {

            $rateMethod = $this->_rateMethodFactory->create();

            $rateMethod->setCarrier(self::CARRIER_CODE);
            $rateMethod->setCarrierTitle($name);

            $rateMethod->setMethod($method['store_code']);
            $rateMethod->setMethodTitle($method['store_name']);

            $rateMethod->setPrice($method['store_price']);
            $rateMethod->setCost($method['store_price']);

            $result->append($rateMethod);

        }

        return $result;
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
