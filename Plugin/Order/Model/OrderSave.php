<?php

namespace Elogic\VendorShipping\Plugin\Order\Model;

use Elogic\VendorShipping\Api\OrderVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Api\QuoteVendorShippingRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

class OrderSave
{
    private $orderVendorShippingRepository;
    private $quoteVendorShippingRepository;

    public function __construct(
        OrderVendorShippingRepositoryInterface $orderVendorShippingRepository,
        QuoteVendorShippingRepositoryInterface $quoteVendorShippingRepository
    )
    {
        $this->orderVendorShippingRepository = $orderVendorShippingRepository;
        $this->quoteVendorShippingRepository = $quoteVendorShippingRepository;
    }

    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    ) {
        try {
            $resultOrder = $this->saveVendorShippingAttribute($resultOrder);
        } catch (CouldNotSaveException $e) {
            // TODO
        }

        return $resultOrder;
    }

    private function saveVendorShippingAttribute(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (
            null !== $extensionAttributes &&
            null !== $extensionAttributes->getOrderVendorShipping()
        ) {
            $quoteRepositoryByOrder = $this->quoteVendorShippingRepository->getByQuoteId($order->getQuoteId());

            $vendorId = $quoteRepositoryByOrder->getVendorId();

            $orderVendorShipping = $extensionAttributes->getOrderVendorShipping();

            // Vendor and Order set
            $orderVendorShipping->setVendorId($vendorId);
            $orderVendorShipping->setOrderId($order->getEntityId());

            try {
                // The actual implementation of the repository is omitted
                // but it is where you would save to the database (or any other persistent storage)
                $this->orderVendorShippingRepository->save($orderVendorShipping);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(
                    __('Could not add attribute to order: "%1"', $e->getMessage()),
                    $e
                );
            }
        }
        return $order;
    }
}
