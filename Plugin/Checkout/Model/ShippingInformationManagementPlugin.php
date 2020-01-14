<?php

namespace Elogic\VendorShipping\Plugin\Checkout\Model;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\QuoteRepository;
use Elogic\VendorShipping\Api\QuoteVendorShippingRepositoryInterface;

/**
 * Class ShippingInformationManagementPlugin
 * @package Elogic\VendorShipping\Model\Checkout
 */
class ShippingInformationManagementPlugin
{
    protected $quoteRepository;
    protected $quoteVendorShippingRepository;

    /**
     * @param QuoteRepository $quoteRepository
     * @param QuoteVendorShippingRepositoryInterface $quoteVendorShippingRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        QuoteVendorShippingRepositoryInterface $quoteVendorShippingRepository
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->quoteVendorShippingRepository = $quoteVendorShippingRepository;
    }

    /**
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @throws NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    )
    {
        if(!$extAttributes = $addressInformation->getExtensionAttributes())
            return;

        $vendorShipping = $extAttributes->getVendorShipping();
        $vendorId = $vendorShipping->getVendorId();

        $quote = $this->quoteRepository->getActive($cartId);
        $quoteId = $quote->getEntityId();

        // If Quote already exist
        if ($this->quoteVendorShippingRepository->getByQuoteId($quoteId)->getQuoteId() > 0) {
            $vendorShipping = $this->quoteVendorShippingRepository->getByQuoteId($quoteId);
        } else {
            $vendorShipping->setQuoteId($quoteId);
        }

        $vendorShipping->setVendorId($vendorId);

        $this->quoteVendorShippingRepository->save($vendorShipping);
    }
}
