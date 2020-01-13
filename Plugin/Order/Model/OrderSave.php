<?php

namespace Elogic\VendorShipping\Plugin\Order\Model;

use Elogic\VendorShipping\Api\OrderVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Api\QuoteVendorShippingRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

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
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        try {
            $resultOrder = $this->saveVendorShippingAttribute($resultOrder);
        } catch (CouldNotSaveException $e) {
            // TODO
        }

        return $resultOrder;
    }

    private function saveVendorShippingAttribute(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if (
            null !== $extensionAttributes
            && null !== $extensionAttributes->getVendorShipping()
        ) {
            $quoteRepositoryByOrder = $this->quoteVendorShippingRepository->getByQuoteId($order->getQuoteId());
            $vendorId = $quoteRepositoryByOrder->getVendorId();
            $orderVendorShipping = $extensionAttributes->getVendorShipping();

            // Set Vendor and Order
            $orderVendorShipping->setVendorId($vendorId);
            $orderVendorShipping->setOrderId($order->getEntityId());

            try {
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
