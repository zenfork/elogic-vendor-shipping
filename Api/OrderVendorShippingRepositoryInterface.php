<?php

namespace Elogic\VendorShipping\Api;

use Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface;

interface OrderVendorShippingRepositoryInterface
{
    /**
     * @param int $id
     * @return OrderVendorShippingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param OrderVendorShippingInterface $vendorShippingAttribute
     * @return OrderVendorShippingInterface
     */
    public function save(OrderVendorShippingInterface $vendorShippingAttribute);

    /**
     * @param OrderVendorShippingInterface $vendorShippingAttribute
     * @return OrderVendorShippingInterface
     *
     */
    public function delete(OrderVendorShippingInterface $vendorShippingAttribute);

    /**
     * @param integer $orderId
     * @return OrderVendorShippingInterface
     */
    public function getByOrderId($orderId);
}
