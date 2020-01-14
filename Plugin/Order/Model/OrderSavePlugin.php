<?php

namespace Elogic\VendorShipping\Plugin\Order\Model;

use Elogic\VendorShipping\Api\OrderVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Api\QuoteVendorShippingRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderSavePlugin
 * @package Elogic\VendorShipping\Plugin\Order\Model
 */
class OrderSavePlugin
{
    /**
     * @var OrderVendorShippingRepositoryInterface
     */
    private $orderVendorShippingRepository;
    /**
     * @var QuoteVendorShippingRepositoryInterface
     */
    private $quoteVendorShippingRepository;

    /**
     * OrderSavePlugin constructor.
     * @param OrderVendorShippingRepositoryInterface $orderVendorShippingRepository
     * @param QuoteVendorShippingRepositoryInterface $quoteVendorShippingRepository
     */
    public function __construct(
        OrderVendorShippingRepositoryInterface $orderVendorShippingRepository,
        QuoteVendorShippingRepositoryInterface $quoteVendorShippingRepository
    )
    {
        $this->orderVendorShippingRepository = $orderVendorShippingRepository;
        $this->quoteVendorShippingRepository = $quoteVendorShippingRepository;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function afterSave(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $resultOrder = $this->saveVendorShippingAttribute($resultOrder);

        return $resultOrder;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
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
                    __(
                        'Could not add vendor shipping attribute to order: "%1"',
                        $e->getMessage()
                    ),
                    $e
                );
            }
        }
        return $order;
    }
}
