<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Processor;

use Generated\Shared\Transfer\QuoteApprovalRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\QuoteApproval\QuoteApprovalClientInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Exception\QuoteApprovalsExceptionFactory;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CartReaderInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CompanyUserResolverInterface;

class QuoteApprovalCancelStorefrontProcessor extends AbstractStorefrontProcessor
{
    protected const string URI_VARIABLE_CART_UUID = 'cartUuid';

    protected const string URI_VARIABLE_QUOTE_APPROVAL_UUID = 'quoteApprovalUuid';

    public function __construct(
        protected QuoteApprovalClientInterface $quoteApprovalClient,
        protected CartReaderInterface $cartReader,
        protected CompanyUserResolverInterface $companyUserResolver,
        protected QuoteApprovalsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): null
    {
        $customerTransfer = $this->getCustomer();
        $companyUserTransfer = $this->companyUserResolver->getCompanyUser($customerTransfer);

        $quoteTransfer = $this->cartReader->getOwnedOrSharedQuote(
            (string)$this->findUriVariable(static::URI_VARIABLE_CART_UUID),
            $customerTransfer,
        );

        if (!$this->cartReader->isOwnedBy($quoteTransfer, $customerTransfer)) {
            throw $this->exceptionFactory->createQuoteApprovalCancelNotPermittedException();
        }

        $quoteApprovalResponseTransfer = $this->quoteApprovalClient->removeQuoteApproval(
            (new QuoteApprovalRequestTransfer())
                ->setIdQuoteApproval($this->getRequestedQuoteApprovalId($quoteTransfer))
                ->setRequesterCompanyUserId($companyUserTransfer->getIdCompanyUserOrFail()),
        );

        // Core Zed refuses a removal only with the cancel permission message, which the mapping holds;
        // the 422 5754 fallback for an unmapped message is unreachable and deliberately undeclared.
        if (!$quoteApprovalResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteApprovalResponse($quoteApprovalResponseTransfer);
        }

        return null;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function getRequestedQuoteApprovalId(QuoteTransfer $quoteTransfer): int
    {
        $requestedQuoteApprovalUuid = (string)$this->findUriVariable(static::URI_VARIABLE_QUOTE_APPROVAL_UUID);

        foreach ($quoteTransfer->getQuoteApprovals() as $quoteApprovalTransfer) {
            if ($quoteApprovalTransfer->getUuid() === $requestedQuoteApprovalUuid) {
                return $quoteApprovalTransfer->getIdQuoteApprovalOrFail();
            }
        }

        throw $this->exceptionFactory->createQuoteApprovalNotFoundException();
    }
}
