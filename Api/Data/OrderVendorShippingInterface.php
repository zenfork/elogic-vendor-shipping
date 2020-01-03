<?php

namespace Elogic\VendorShipping\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface OrderVendorShippingInterface extends ExtensibleDataInterface
{
    const ID = 'id';
    const VENDOR_ID = 'vendor_id';
    const ORDER_ID = 'order_id';

    /**
     * Return value.
     *
     * @return string|null
     */
    public function getId();

    /**
     * Set value.
     *
     * @param string|null $value
     * @return $this
     */
    public function setId($value);

    /**
     * Return value.
     *
     * @return string|null
     */
    public function getVendorId();

    /**
     * Set value.
     *
     * @param string|null $value
     * @return $this
     */
    public function setVendorId($value);

    /**
     * Return value.
     *
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set value.
     *
     * @param string|null $value
     * @return $this
     */
    public function setOrderId($value);
}
