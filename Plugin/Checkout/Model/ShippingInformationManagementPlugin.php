<?php

namespace Elogic\VendorShipping\Plugin\Checkout\Model;

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

        $vendorShipping = $extAttributes->getVendorShipping();
        $vendorId = $vendorShipping->getVendorId();

        $quote = $this->quoteRepository->getActive($cartId);
        $quoteId = $quote->getEntityId();

        // if quote already exist
        if ($this->quoteVendorShippingRepository->getByQuoteId($quoteId)->getQuoteId() > 0) {
            $vendorShipping = $this->quoteVendorShippingRepository->getByQuoteId($quoteId);
        } else {
            $vendorShipping->setQuoteId($quoteId);
        }

        $vendorShipping->setVendorId($vendorId);
//        $extAttributes->setCheckoutVendorShipping($vendorShipping);

        $this->quoteVendorShippingRepository->save($vendorShipping);

        // set ext attr to quote
//        if(!$extAttributes = $quote->getExtensionAttributes())
//            return;

        /*
        $extAttributes = $quote->getExtensionAttributes();
        $vendorShipping = $extAttributes->getVendorShipping();
        $vendorShipping->setVendorId($vendorId);
        $quote->setExtensionAttributes($extAttributes);

        $this->quoteRepository->save($quote);
        */
    }
}
