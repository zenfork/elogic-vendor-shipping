<?php
/**
 * *
 *  @author DCKAP Team
 *  @copyright Copyright (c) 2018 DCKAP (https://www.dckap.com)
 *  @package Dckap_CustomFields
 */
namespace Elogic\VendorShipping\Observer;
use Elogic\VendorShipping\Api\QuoteVendorShippingRepositoryInterface;
use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\ObserverInterface;
use Elogic\VendorShipping\Model\OrderVendorShippingFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;

/**
 * Class SaveCustomFieldsInOrder
 * @package Dckap\CustomFields\Observer
 */
class SaveExtensionAttributesInOrder implements ObserverInterface
{
    /**
     * @var Copy
     */
    protected $objectCopyService;

    /**
     * @var QuoteVendorShippingRepositoryInterface
     */
    protected $quoteVendorShippingRepository;
    /**
     * @var OrderVendorShippingFactory
     */
    private $orderVendorShippingFactory;

    private $orderExtensionFactory;

    /**
     * @param Copy $objectCopyService
     * @param QuoteVendorShippingRepositoryInterface $quoteVendorShippingRepository
     * @param OrderVendorShippingFactory $orderVendorShippingFactory
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        Copy $objectCopyService,
        QuoteVendorShippingRepositoryInterface $quoteVendorShippingRepository,
        OrderVendorShippingFactory $orderVendorShippingFactory,
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->objectCopyService = $objectCopyService;
        $this->quoteVendorShippingRepository = $quoteVendorShippingRepository;
        $this->orderVendorShippingFactory = $orderVendorShippingFactory;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $quoteId = $quote->getEntityId();
        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $vendorShippingAttribute = $this->orderVendorShippingFactory->create();

        if ($this->quoteVendorShippingRepository->getByQuoteId($quoteId)->getQuoteId() > 0) {
            $vendorShipping = $this->quoteVendorShippingRepository->getByQuoteId($quoteId);
            $vendorId = $vendorShipping->getVendorId();
            $vendorShippingAttribute->setVendorId($vendorId);
            $orderExtension->setVendorShipping($vendorShippingAttribute);
            $order->setExtensionAttributes($orderExtension);
        }

//        $this->objectCopyService->copyFieldsetToTarget('sales_convert_quote', 'to_order', $quote, $order);

        return $this;
    }
}
