<?php

namespace Elogic\VendorShipping\Model;

use Magento\Framework\Model\AbstractModel;
use Elogic\VendorShipping\Api\Data\OrderVendorShippingInterface;

class OrderVendorShipping extends AbstractModel implements OrderVendorShippingInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\OrderVendorShipping::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getVendorId()
    {
        return $this->getData(self::VENDOR_ID);
    }

    /**
     * @inheritDoc
     */
    public function setVendorId($value)
    {
        return $this->setData(self::VENDOR_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }
}
