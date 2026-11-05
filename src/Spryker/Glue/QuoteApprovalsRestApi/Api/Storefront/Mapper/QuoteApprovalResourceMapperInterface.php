<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Mapper;

use Generated\Api\Storefront\QuoteApprovalsStorefrontResource;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteApprovalTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCustomerTransfer;

interface QuoteApprovalResourceMapperInterface
{
    public function mapQuoteApprovalTransferToResource(
        QuoteApprovalTransfer $quoteApprovalTransfer,
        QuoteApprovalsStorefrontResource $quoteApprovalsStorefrontResource,
        string $cartUuid,
    ): QuoteApprovalsStorefrontResource;

    public function mapResourceToRestCheckoutRequestAttributesTransfer(
        QuoteApprovalsStorefrontResource $quoteApprovalsStorefrontResource,
        RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer,
    ): RestCheckoutRequestAttributesTransfer;

    public function mapCompanyUserTransferToRestCustomerTransfer(
        CustomerTransfer $customerTransfer,
        CompanyUserTransfer $companyUserTransfer,
        RestCustomerTransfer $restCustomerTransfer,
    ): RestCustomerTransfer;
}
