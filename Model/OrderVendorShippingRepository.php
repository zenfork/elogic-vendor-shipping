<?php

namespace Elogic\VendorShipping\Model;

use Elogic\VendorShipping\Api\OrderVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Model\ResourceModel\QuoteVendorShipping as VendorShippingResource;
use Elogic\VendorShipping\Model\OrderVendorShippingFactory;
use Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface;

/**
 * Class OrderVendorShippingRepository
 * @package Elogic\VendorShipping\Model
 */
class OrderVendorShippingRepository implements OrderVendorShippingRepositoryInterface
{
    /**
     * @var VendorShippingResource
     */
    private $resource;

    /**
     * @var OrderVendorShippingFactory
     */
    private $orderVendorShippingFactory;

    /**
     * OrderVendorShippingRepository constructor.
     * @param VendorShippingResource $resource
     * @param OrderVendorShippingFactory $orderVendorShippingFactory
     */
    public function __construct(
        VendorShippingResource $resource,
        OrderVendorShippingFactory $orderVendorShippingFactory
    )
    {
        $this->resource = $resource;
        $this->orderVendorShippingFactory = $orderVendorShippingFactory;
    }

    /**
     * @param int $id
     * @return \Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface
     */
    public function getById($id)
    {
        $vendorShippingAttribute = $this->orderVendorShippingFactory->create();
        $this->resource->load($vendorShippingAttribute, $id);
        return $vendorShippingAttribute;
    }

    /**
     * @param \Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface $vendorShippingAttribute
     * @return OrderVendorShippingRepository
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(OrderVendorShippingInterface $vendorShippingAttribute)
    {
        $vendorShippingAttribute->getResource()->save($vendorShippingAttribute);
        return $this;
    }

    /**
     * @param \Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface $vendorShippingAttribute
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
     * @return \Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface
     */
    public function getByOrderId($orderId){
        $vendorShippingAttribute = $this->orderVendorShippingFactory->create();
        $this->resource->load($vendorShippingAttribute, $orderId, 'order_id');
        return $vendorShippingAttribute;
    }
}
