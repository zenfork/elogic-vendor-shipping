<?php

namespace Elogic\VendorShipping\Model;

use Elogic\VendorShipping\Api\QuoteVendorShippingRepositoryInterface;
use Elogic\VendorShipping\Api\Data\QuoteVendorShippingInterface;
use Elogic\VendorShipping\Model\ResourceModel\QuoteVendorShipping as QuoteVendorShippingResource;
use Elogic\VendorShipping\Model\QuoteVendorShippingFactory;

/**
 * Class QuoteVendorShippingRepository
 * @package Elogic\VendorShipping\Model
 */
class QuoteVendorShippingRepository implements QuoteVendorShippingRepositoryInterface
{
    /**
     * @var QuoteVendorShippingResource
     */
    private $resource;

    /**
     * @var QuoteVendorShippingFactory
     */
    private $quoteVendorShippingFactory;

    /**
     * QuoteVendorShippingRepository constructor.
     * @param QuoteVendorShippingResource $resource
     * @param QuoteVendorShippingFactory $quoteVendorShippingFactory
     */
    public function __construct(
        QuoteVendorShippingResource $resource,
        QuoteVendorShippingFactory $quoteVendorShippingFactory
    )
    {
        $this->resource = $resource;
        $this->quoteVendorShippingFactory = $quoteVendorShippingFactory;
    }

    /**
     * @param int $id
     * @return \Elogic\VendorShipping\Api\Data\QuoteVendorShippingInterface
     */
    public function getById($id)
    {
        $vendorShippingAttribute = $this->quoteVendorShippingFactory->create();
        $this->resource->load($vendorShippingAttribute, $id);
        return $vendorShippingAttribute;
    }

    /**
     * @param \Elogic\VendorShipping\Api\Data\QuoteVendorShippingInterface $vendorShippingAttribute
     * @return QuoteVendorShippingRepository
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(QuoteVendorShippingInterface $vendorShippingAttribute)
    {
        $vendorShippingAttribute->getResource()->save($vendorShippingAttribute);
        return $this;
    }

    /**
     * @param \Elogic\VendorShipping\Api\Data\QuoteVendorShippingInterface $vendorShippingAttribute
     * @return QuoteVendorShippingRepository
     * @throws \Exception
     */
    public function delete(QuoteVendorShippingInterface $vendorShippingAttribute)
    {
        $vendorShippingAttribute->getResource()->delete($vendorShippingAttribute);
        return $this;
    }

    /**
     * @param integer $quoteId
     * @return \Elogic\VendorShipping\Api\Data\QuoteVendorShippingInterface
     */
    public function getByQuoteId($quoteId){
        $vendorShippingAttribute = $this->quoteVendorShippingFactory->create();
        $this->resource->load($vendorShippingAttribute, $quoteId, 'quote_id');
        return $vendorShippingAttribute;
    }
}
