<?php

namespace Elogic\VendorShipping\Plugin\Quote\Model;

use Magento\Quote\Model\QuoteRepository;
use Elogic\VendorShipping\Api\QuoteVendorShippingRepositoryInterface;

/**
 * Class ShippingInformationManagementPlugin
 * @package Elogic\VendorShipping\Model\Checkout
 */
class SaveToQuote
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
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        if(!$extAttributes = $addressInformation->getExtensionAttributes())
            return;

        $vendorShipping = $extAttributes->getQuoteVendorShipping();
        $vendorId = $vendorShipping->getVendorId();

        $quote = $this->quoteRepository->getActive($cartId);
        $quoteId = $quote->getEntityId();

        // if quote already exist
        if ($this->quoteVendorShippingRepository->getByQuoteId($quoteId)) {
            $vendorShipping = $this->quoteVendorShippingRepository->getByQuoteId($quoteId);
        } else {
            $vendorShipping->setQuoteId($quoteId);
        }

        $vendorShipping->setVendorId($vendorId);

        $extAttributes->setQuoteVendorShipping($vendorShipping);
        $this->quoteVendorShippingRepository->save($vendorShipping);

//        $this->quoteRepository->save($quote);
    }
}
