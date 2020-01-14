<?php
namespace Elogic\VendorShipping\Plugin\Checkout\Model;
use Elogic\Vendor\Api\VendorRepositoryInterface;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DefaultConfigProviderPlugin
 * @package Elogic\VendorShipping\Plugin\Checkout\Model
 */
class DefaultConfigProviderPlugin
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var VendorRepositoryInterface
     */
    private $vendorRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     * @param VendorRepositoryInterface $vendorRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param $storeManager
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        VendorRepositoryInterface $vendorRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->vendorRepository = $vendorRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * @param DefaultConfigProvider $subject
     * @param array $result
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetConfig(
        DefaultConfigProvider $subject,
        array $result
    ) {
        $vendors_id = [];
        $items = $result['totalsData']['items'];

        // Getting vendors info from items
        foreach ($items as $index => $item) {
            $quoteItem = $this->checkoutSession->getQuote()->getItemById($item['item_id']);
            $vendors_id[] = explode(',', $quoteItem->getProduct()
                ->getData('elogic_vendor'));
        }

        // Filter vendors that only have intersect by items
        if (count($vendors_id) > 1) {
            $vendors_id = call_user_func_array('array_intersect', $vendors_id);
        } else {
            $vendors_id = reset($vendors_id);
        }

        // Getting info for founded vendors
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('id', $vendors_id, 'in')
            ->create();
        $repo = $this->vendorRepository->getList($searchCriteria);
        foreach ($repo->getItems() as $item) {
            $result['vendors'][] = $item->getData();
        }

        // Media URL
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        $result['mediaUrl'] = $mediaUrl;

        return $result;
    }
}
