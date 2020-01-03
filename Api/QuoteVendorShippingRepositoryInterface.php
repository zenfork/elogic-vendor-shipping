<?php

namespace Elogic\VendorShipping\Api;

use Elogic\VendorShipping\Api\Data\QuoteVendorShippingInterface;

interface QuoteVendorShippingRepositoryInterface
{
    /**
     * @param int $id
     * @return QuoteVendorShippingInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param QuoteVendorShippingInterface $vendorShippingAttribute
     * @return QuoteVendorShippingInterface
     */
    public function save(QuoteVendorShippingInterface $vendorShippingAttribute);

    /**
     * @param QuoteVendorShippingInterface $vendorShippingAttribute
     * @return QuoteVendorShippingInterface
     *
     */
    public function delete(QuoteVendorShippingInterface $vendorShippingAttribute);

    /**
     * @param integer $quoteId
     * @return QuoteVendorShippingInterface
     */
    public function getByQuoteId($quoteId);
}
