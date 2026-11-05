<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Checker;

use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Spryker\Client\CompanyRole\CompanyRoleClientInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Exception\QuoteApprovalsExceptionFactory;

/**
 * Reads the permission off the company roles of the company user instead of the permission client,
 * which answers from the session or the access token of the current PHP request and caches the
 * first answer per process.
 */
class QuoteApprovalRequestPermissionChecker implements QuoteApprovalRequestPermissionCheckerInterface
{
    /**
     * @uses \Spryker\Client\QuoteApproval\Plugin\Permission\RequestQuoteApprovalPermissionPlugin::KEY
     */
    protected const string PERMISSION_KEY_REQUEST_QUOTE_APPROVAL = 'RequestQuoteApprovalPermissionPlugin';

    public function __construct(
        protected CompanyRoleClientInterface $companyRoleClient,
        protected QuoteApprovalsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function assertMayRequestQuoteApproval(CompanyUserTransfer $companyUserTransfer): void
    {
        $companyRoleCollectionTransfer = $this->companyRoleClient->getCompanyRoleCollection(
            (new CompanyRoleCriteriaFilterTransfer())->setIdCompanyUser($companyUserTransfer->getIdCompanyUserOrFail()),
        );

        foreach ($companyRoleCollectionTransfer->getRoles() as $companyRoleTransfer) {
            foreach ($companyRoleTransfer->getPermissionCollection()?->getPermissions() ?? [] as $permissionTransfer) {
                if ($permissionTransfer->getKey() === static::PERMISSION_KEY_REQUEST_QUOTE_APPROVAL) {
                    return;
                }
            }
        }

        throw $this->exceptionFactory->createRequestQuoteApprovalPermissionMissingException();
    }
}
