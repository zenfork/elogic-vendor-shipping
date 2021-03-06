<?php

namespace Elogic\VendorShipping\Model;

use Elogic\VendorShipping\Api\OrderVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface;
use Elogic\VendorShipping\Model\ResourceModel\OrderVendorShipping as OrderVendorShippingResource;
use Elogic\VendorShipping\Model\OrderVendorShippingFactory;

/**
 * Class OrderVendorShippingRepository
 * @package Elogic\VendorShipping\Model
 */
class OrderVendorShippingRepository implements OrderVendorShippingRepositoryInterface
{
    /**
     * @var OrderVendorShippingResource
     */
    private $resource;

    /**
     * @var OrderVendorShippingFactory
     */
    private $orderVendorShippingFactory;

    /**
     * OrderVendorShippingRepository constructor.
     * @param OrderVendorShippingResource $resource
     * @param OrderVendorShippingFactory $orderVendorShippingFactory
     */
    public function __construct(
        OrderVendorShippingResource $resource,
        OrderVendorShippingFactory $orderVendorShippingFactory
    )
    {
        $this->resource = $resource;
        $this->orderVendorShippingFactory = $orderVendorShippingFactory;
    }

    /**
     * @param int $id
     * @return OrderVendorShippingInterface
     */
    public function getById($id)
    {
        $vendorShippingAttribute = $this->orderVendorShippingFactory->create();
        $this->resource->load($vendorShippingAttribute, $id);
        return $vendorShippingAttribute;
    }

    /**
     * @param OrderVendorShippingInterface $vendorShippingAttribute
     * @return OrderVendorShippingRepository
     * @throws \Exception
     */
    public function save(OrderVendorShippingInterface $vendorShippingAttribute)
    {
        $vendorShippingAttribute->getResource()->save($vendorShippingAttribute);
        return $this;
    }

    /**
     * @param OrderVendorShippingInterface $vendorShippingAttribute
     * @return OrderVendorShippingRepository
     * @throws \Exception
     */
    public function delete(OrderVendorShippingInterface $vendorShippingAttribute)
    {
        $vendorShippingAttribute->getResource()->delete($vendorShippingAttribute);
        return $this;
    }

    /**
     * @param integer $orderId
     * @return OrderVendorShippingInterface
     */
    public function getByOrderId($orderId)
    {
        $vendorShippingAttribute = $this->orderVendorShippingFactory->create();
        $this->resource->load($vendorShippingAttribute, $orderId, 'order_id');
        return $vendorShippingAttribute;
    }
}
