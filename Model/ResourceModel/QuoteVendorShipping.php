<?php

namespace Elogic\VendorShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class QuoteVendorShipping
 * @package Elogic\VendorShipping\Model\ResourceModel
 */
class QuoteVendorShipping extends AbstractDb {
    /**
     * Constructor
     */
    protected function _construct() {
        $this->_init('elogic_quote_vendor_shipping', 'id');
    }
}
