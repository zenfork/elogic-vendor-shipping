<?php

namespace Elogic\VendorShipping\Plugin\Order\Model;

use Elogic\VendorShipping\Api\OrderVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Model\OrderVendorShippingFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderGetPlugin
 * @package Elogic\VendorShipping\Plugin\Order\Model
 */
class OrderGetPlugin
{
    /**
     * @var OrderVendorShippingRepositoryInterface
     */
    private $vendorShippingRepository;
    /**
     * @var OrderVendorShippingFactory
     */
    private $orderVendorShippingFactory;
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * OrderGetPlugin constructor.
     * @param OrderVendorShippingRepositoryInterface $vendorShippingRepository
     * @param OrderVendorShippingFactory $orderVendorShippingFactory
     * @param OrderExtensionFactory $orderExtensionFactory
     */
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

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $resultOrder = $this->getVendorShippingAttribute($resultOrder);

        return $resultOrder;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    private function getVendorShippingAttribute(OrderInterface $order)
    {
        try {
            $vendorShippingRepository = $this->vendorShippingRepository->getByOrderId($order->getEntityId());
        } catch (NoSuchEntityException $e) {
            return $order;
        }

        $extensionAttributes = $order->getExtensionAttributes();
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $vendorShippingAttribute = $this->orderVendorShippingFactory->create();

        if ($vendorId = $vendorShippingRepository->getVendorId()) {
            $vendorShippingAttribute->setVendorId($vendorId);
            $orderExtension->setVendorShipping($vendorShippingAttribute);
            $order->setExtensionAttributes($orderExtension);
        }

        return $order;
    }
}
