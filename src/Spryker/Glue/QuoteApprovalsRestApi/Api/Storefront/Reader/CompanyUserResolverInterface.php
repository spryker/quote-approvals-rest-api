<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

interface CompanyUserResolverInterface
{
    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function getCompanyUser(CustomerTransfer $customerTransfer): CompanyUserTransfer;

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function assertCompanyUserSelected(CustomerTransfer $customerTransfer): void;
}
