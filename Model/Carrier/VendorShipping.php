<?php
namespace Elogic\VendorShipping\Model\Carrier;

/**
 * Class VendorShipping
 * @package Elogic\VendorShipping\Model\Carrier
 */
class VendorShipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface {
    const CARRIER_CODE = 'vendorshipping';
    const VENDOR_SHIPPING_STANDARD = 'vendorshippingstandard';
    const VENDOR_SHIPPING_48H = 'vendorshipping48h';
    protected $_code = self::CARRIER_CODE;
    protected $_isFixed = true;
    protected $_rateResultFactory;
    protected $_rateMethodFactory;

    /**
     * VendorShipping constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return bool|\Magento\Framework\DataObject|\Magento\Shipping\Model\Rate\Result|null
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $this->appendMethodToRateResult(self::VENDOR_SHIPPING_STANDARD, $result);
        $this->appendMethodToRateResult(self::VENDOR_SHIPPING_48H, $result);

        return $result;
    }
    public function getAllowedMethods() {
        return [
            self::VENDOR_SHIPPING_STANDARD => $this->getConfigData(self::VENDOR_SHIPPING_STANDARD . '/title'),
            self::VENDOR_SHIPPING_48H => $this->getConfigData(self::VENDOR_SHIPPING_48H . '/title'),
        ];
    }
    private function getMethodTitle($method) {
        return $this->getConfigData($method . '/title');
    }
    private function getMethodPrice($method) {
        return $this->getMethodCost($method);
    }
    private function getMethodCost($method) {
        return $this->getConfigData($method . '/shippingcost');
    }

    /**
     * @param string $methodName
     * @param \Magento\Shipping\Model\Rate\Result $result
     * @return \Magento\Shipping\Model\Rate\Result
     */
    private function appendMethodToRateResult($methodName, $result) {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($methodName);
        $method->setMethodTitle($this->getMethodTitle($method->getMethod()));
        $method->setPrice($this->getMethodPrice($method->getMethod()));
        $method->setCost($this->getMethodCost($method->getMethod()));
        $method->setErrorMessage(__('The %1 method error message here.'));
        return $result->append($method);
    }
}