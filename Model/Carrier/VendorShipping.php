<?php
namespace Elogic\VendorShipping\Model\Carrier;
use Elogic\Vendor\Helper\Data;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class VendorShipping
 * @package Elogic\VendorShipping\Model\Carrier
 */
class VendorShipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface {
    /**
     * Carrier code
     */
    const CARRIER_CODE = 'vendorshipping';
    /**
     * Vendor shipping standard code
     */
    const VENDOR_SHIPPING_STANDARD = 'vendorshippingstandard';
    /**
     * Vendor shipping 48h code
     */
    const VENDOR_SHIPPING_48H = 'vendorshipping48h';
    /**
     * @var string
     */
    protected $_code = self::CARRIER_CODE;
    /**
     * @var bool
     */
    protected $_isFixed = true;
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;
    /**
     * @var Data
     */
    private $vendorHelperData;
    /**
     * @var \Elogic\Vendor\Api\VendorRepositoryInterface
     */
    private $vendorRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * VendorShipping constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param Data $vendorHelperData
     * @param \Elogic\Vendor\Api\VendorRepositoryInterface $vendorRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Elogic\Vendor\Helper\Data $vendorHelperData,
        \Elogic\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        array $data = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->vendorHelperData = $vendorHelperData;
        $this->vendorRepository = $vendorRepository;
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

        // Getting vendors from attribute

        $simpleItems = $this->getAllSimpleItems($request);
        $vendors_id = [];

        foreach ($simpleItems as $id => $item) {
            $product = $item->getProduct();
//            $product->load($item->getProduct()->getId(), [Data::VENDOR_ATTR]);
            $product->getResource()->load($product, $item->getProduct()->getId(), [Data::VENDOR_ATTR]);

            $vendorRepository = $this->vendorRepository;
//            $rep_ids[] = $vendorRepository->getById();

            $vendors_id[] = explode(',', $product->getData(Data::VENDOR_ATTR));
//            $vendors[$id]['vendors_data'] = $this->vendorHelperData->getVendorsByIds($product->getData(Data::VENDOR_ATTR))->getData();
        }

        $ids_intersected_for_each_product = call_user_func_array('array_intersect', $vendors_id);
        $vendors_data = $this->vendorHelperData->getVendorsByIds($ids_intersected_for_each_product)->getData();

//        $searchCriteria = $this->searchCriteriaBuilder->create();
//        $attributeRepository = $this->vendorRepository->getList(
//            $searchCriteria
//        );
//
//        foreach ($attributeRepository->getItems() as $id => $items) {
//            $attr[$id]['attr_code'] = $items->getAttributeCode();
//            $attr[$id]['fr_label'] = $items->getFrontendLabel();
//        }

//        static methods

//        $this->appendMethodToRateResult(self::VENDOR_SHIPPING_STANDARD, $result);
//        $this->appendMethodToRateResult(self::VENDOR_SHIPPING_48H, $result);

        foreach ($vendors_data as $vendor_data) {
            $this->appendMethodToRateResult(
                "vendor_" . $vendor_data['id'],
                $result,
                [
                    'name' => $vendor_data['name'],
                    'price' => '7.99',
                    'cost' => '7.10'
                ]
            );
        }
        return $result;
    }

    /**
     * Get configurable and single prod
     *
     * @param RateRequest $request
     * @return array
     */
    public function getAllSimpleItems(RateRequest $request) {
        $items = [];
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                /* @var $item \Magento\Quote\Model\Quote\Item */
                if ($item->getProduct()->isVirtual() || $item->getParentItem()
                    || $item->getHasChildren() && $item->isShipSeparately()) {
                    // Continue has children, virtual items, and ship separately
                    continue;
                } else {
                    // Ship together - count compound item as one solid
                    $items[] = $item;
                }
            }
        }
        return $items;
    }

    /**
     * @return array
     */
    public function getAllowedMethods() {
        return [
            self::VENDOR_SHIPPING_STANDARD => $this->getConfigData(self::VENDOR_SHIPPING_STANDARD . '/title'),
            self::VENDOR_SHIPPING_48H => $this->getConfigData(self::VENDOR_SHIPPING_48H . '/title'),
        ];
    }

    /**
     * @param $method
     * @return false|string
     */
    private function getMethodTitle($method) {
        return $this->getConfigData($method . '/title');
    }

    /**
     * @param $method
     * @return false|string
     */
    private function getMethodPrice($method) {
        return $this->getMethodCost($method);
    }

    /**
     * @param $method
     * @return false|string
     */
    private function getMethodCost($method) {
        return $this->getConfigData($method . '/shippingcost');
    }

    /**
     * @param string $methodName
     * @param \Magento\Shipping\Model\Rate\Result $result
     * @param bool $customMethod
     * @return \Magento\Shipping\Model\Rate\Result
     */
    private function appendMethodToRateResult($methodName, $result, $customMethod = []) {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($methodName);
        $method->setMethodTitle(
            empty($customMethod['name']) ? $this->getMethodTitle($method->getMethod()) : $customMethod['name']
        );
        $method->setPrice(
            empty($customMethod['price']) ? $this->getMethodPrice($method->getMethod()) : $customMethod['price']
        );
        $method->setCost(
            empty($customMethod['cost']) ? $this->getMethodCost($method->getMethod()) : $customMethod['cost']
        );
        $method->setErrorMessage(__('The %1 method error message here.'));
        return $result->append($method);
    }
}