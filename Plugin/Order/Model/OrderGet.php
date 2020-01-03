<?php

namespace Elogic\VendorShipping\Plugin\Order\Model;

use Elogic\VendorShipping\Api\OrderVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Model\OrderVendorShippingFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtensionFactory;

class OrderGet
{
    private $vendorShippingRepository;
    private $orderVendorShippingFactory;
    private $orderExtensionFactory;

    public function __construct(
        OrderVendorShippingRepositoryInterface $vendorShippingRepository,
        OrderVendorShippingFactory $orderVendorShippingFactory,
        OrderExtensionFactory $orderExtensionFactory
    )
    {
        $this->vendorShippingRepository = $vendorShippingRepository;
        $this->orderVendorShippingFactory = $orderVendorShippingFactory;
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $resultOrder
    ) {
        $resultOrder = $this->getVendorShippingAttribute($resultOrder);

        return $resultOrder;
    }

    private function getVendorShippingAttribute(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        try {
            // The actual implementation of the repository is omitted
            // but it is where you would load your value from the database (or any other persistent storage)
            $vendorShippingRepository = $this->vendorShippingRepository->getByOrderId($order->getEntityId());
        } catch (NoSuchEntityException $e) {
            return $order;
        }

        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $vendorShippingAttribute = $this->orderVendorShippingFactory->create();

        $vendorId = $vendorShippingRepository->getVendorId();

        $vendorShippingAttribute->setVendorId($vendorId);
        $orderExtension->setOrderVendorShipping($vendorShippingAttribute);
        $order->setExtensionAttributes($orderExtension);

        return $order;
    }
}
