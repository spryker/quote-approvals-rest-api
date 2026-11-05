<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Mapper;

use Generated\Api\Storefront\CartsStorefrontResource;
use Generated\Api\Storefront\QuoteApprovalsStorefrontResource;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteApprovalTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCustomerTransfer;

class QuoteApprovalResourceMapper implements QuoteApprovalResourceMapperInterface
{
    protected const string NAME_SEPARATOR = ' ';

    protected const string KEY_BILLING_ADDRESS = 'billingAddress';

    protected const string KEY_SHIPPING_ADDRESS = 'shippingAddress';

    protected const string KEY_SHIPMENT = 'shipment';

    protected const string KEY_PAYMENTS = 'payments';

    public function mapQuoteApprovalTransferToResource(
        QuoteApprovalTransfer $quoteApprovalTransfer,
        QuoteApprovalsStorefrontResource $quoteApprovalsStorefrontResource,
        string $cartUuid,
    ): QuoteApprovalsStorefrontResource {
        $approverCompanyUserTransfer = $quoteApprovalTransfer->getApprover();
        $approverCustomerTransfer = $approverCompanyUserTransfer?->getCustomer();

        return $quoteApprovalsStorefrontResource
            ->setQuoteApprovalUuid($quoteApprovalTransfer->getUuid())
            ->setStatus($quoteApprovalTransfer->getStatus())
            ->setApproverCompanyUserUuid($approverCompanyUserTransfer?->getUuid())
            ->setApproverName($approverCustomerTransfer === null ? null : trim(implode(static::NAME_SEPARATOR, [
                $approverCustomerTransfer->getFirstName(),
                $approverCustomerTransfer->getLastName(),
            ])))
            ->setCreatedAt($quoteApprovalTransfer->getCreatedAt())
            ->setUpdatedAt($quoteApprovalTransfer->getUpdatedAt())
            ->setCart((new CartsStorefrontResource())->setUuid($cartUuid));
    }

    public function mapResourceToRestCheckoutRequestAttributesTransfer(
        QuoteApprovalsStorefrontResource $quoteApprovalsStorefrontResource,
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer,
    ): RestCheckoutRequestAttributesTransfer {
        $checkoutData = array_filter(
            array_intersect_key($quoteApprovalsStorefrontResource->toArray(), array_flip([
                static::KEY_BILLING_ADDRESS,
                static::KEY_SHIPPING_ADDRESS,
                static::KEY_SHIPMENT,
                static::KEY_PAYMENTS,
            ])),
            static fn (mixed $value): bool => $value !== null,
        );

        return $restCheckoutRequestAttributesTransfer->fromArray($checkoutData, true);
    }

    public function mapCompanyUserTransferToRestCustomerTransfer(
        CustomerTransfer $customerTransfer,
        CompanyUserTransfer $companyUserTransfer,
        RestCustomerTransfer $restCustomerTransfer,
    ): RestCustomerTransfer {
        return $restCustomerTransfer
            ->setIdCustomer($customerTransfer->getIdCustomer())
            ->setCustomerReference($customerTransfer->getCustomerReferenceOrFail())
            ->setIdCompanyUser($companyUserTransfer->getIdCompanyUserOrFail())
            ->setIdCompany($companyUserTransfer->getFkCompanyOrFail())
            ->setIdCompanyBusinessUnit($companyUserTransfer->getFkCompanyBusinessUnitOrFail())
            ->setCompanyUserTransfer($companyUserTransfer);
    }
}
