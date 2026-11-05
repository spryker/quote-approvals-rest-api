<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Exception\QuoteApprovalsExceptionFactory;

class CompanyUserResolver implements CompanyUserResolverInterface
{
    public function __construct(protected QuoteApprovalsExceptionFactory $exceptionFactory)
    {
    }

    public function getCompanyUser(CustomerTransfer $customerTransfer): CompanyUserTransfer
    {
        $this->assertCompanyUserSelected($customerTransfer);

        return $customerTransfer->getCompanyUserTransferOrFail();
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function assertCompanyUserSelected(CustomerTransfer $customerTransfer): void
    {
        $companyUserTransfer = $customerTransfer->getCompanyUserTransfer();

        if (
            $companyUserTransfer === null
            || $companyUserTransfer->getIdCompanyUser() === null
            || $companyUserTransfer->getFkCompany() === null
            || $companyUserTransfer->getFkCompanyBusinessUnit() === null
        ) {
            throw $this->exceptionFactory->createCompanyUserNotSelectedException();
        }
    }
}
