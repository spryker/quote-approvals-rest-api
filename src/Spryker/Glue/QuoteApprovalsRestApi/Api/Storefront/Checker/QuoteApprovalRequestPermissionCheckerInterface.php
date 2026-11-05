<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Checker;

use Generated\Shared\Transfer\CompanyUserTransfer;

interface QuoteApprovalRequestPermissionCheckerInterface
{
    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function assertMayRequestQuoteApproval(CompanyUserTransfer $companyUserTransfer): void;
}
