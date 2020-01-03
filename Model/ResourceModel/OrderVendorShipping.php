<?php

namespace Elogic\VendorShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class OrderVendorShipping
 * @package Elogic\VendorShipping\Model\ResourceModel
 */
class OrderVendorShipping extends AbstractDb {
    /**
     * Constructor
     */
    protected function _construct() {
        $this->_init('elogic_order_vendor_shipping', 'id');
    }
}
