<?php

namespace Elogic\VendorShipping\Model;

use Magento\Framework\Model\AbstractModel;
use Elogic\VendorShipping\Api\Data\QuoteVendorShippingInterface;

class QuoteVendorShipping extends AbstractModel implements QuoteVendorShippingInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\QuoteVendorShipping::class);
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
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setQuoteId($value)
    {
        return $this->setData(self::QUOTE_ID, $value);
    }
}
