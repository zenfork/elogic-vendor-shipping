<?php
namespace Elogic\VendorShipping\Plugin\Checkout\Model;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class DefaultConfigProvider
 * @package Elogic\VendorShipping\Plugin\Checkout\Model
 */
class DefaultConfigProvider
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Elogic\Vendor\Api\VendorRepositoryInterface
     */
    private $vendorRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     * @param \Elogic\Vendor\Api\VendorRepositoryInterface $vendorRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param $storeManager
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        \Elogic\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->vendorRepository = $vendorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        // Vendors data
        $vendors_id = [];
        $items = $result['totalsData']['items'];
        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $vendors_id[] = $quoteItem->getProduct()
                ->getData('elogic_vendor');
        }
        if (count($vendors_id) > 1) {
            $vendors_id = call_user_func_array('array_intersect', $vendors_id);
        } else {
            $vendors_id = reset($vendors_id);
        }
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('id', $vendors_id, 'in')
            ->create();
        $repo = $this->vendorRepository->getList($searchCriteria);
        foreach ($repo->getItems() as $item) {
            $result['totalsData']['vendors'][] = $item->getData();
        }

        // Media URL
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $result['mediaUrl'] = $mediaUrl;

        return $result;
    }
}