<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\CartsRestApi\CartsRestApiClientInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Exception\QuoteApprovalsExceptionFactory;

class CartReader implements CartReaderInterface
{
    public function __construct(
        protected CartsRestApiClientInterface $cartsRestApiClient,
        protected QuoteApprovalsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function getOwnedQuote(string $cartUuid, CustomerTransfer $customerTransfer): QuoteTransfer
    {
        $quoteTransfer = $this->getReadableQuote($cartUuid, $customerTransfer);

        if (!$this->isOwnedBy($quoteTransfer, $customerTransfer)) {
            throw $this->exceptionFactory->createCartNotFoundException();
        }

        return $quoteTransfer;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function getOwnedOrSharedQuote(string $cartUuid, CustomerTransfer $customerTransfer): QuoteTransfer
    {
        $quoteTransfer = $this->getReadableQuote($cartUuid, $customerTransfer);

        if (!$this->isOwnedBy($quoteTransfer, $customerTransfer) && $quoteTransfer->getQuotePermissionGroup() === null) {
            throw $this->exceptionFactory->createCartNotFoundException();
        }

        return $quoteTransfer;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function getReadableQuote(string $cartUuid, CustomerTransfer $customerTransfer): QuoteTransfer
    {
        $quoteResponseTransfer = $this->cartsRestApiClient->findQuoteByUuid(
            (new QuoteTransfer())
                ->setUuid($cartUuid)
                ->setCustomerReference($customerTransfer->getCustomerReferenceOrFail())
                ->setCustomer($customerTransfer),
        );
        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if (!$quoteResponseTransfer->getIsSuccessful() || $quoteTransfer === null) {
            throw $this->exceptionFactory->createCartNotFoundException();
        }

        return $quoteTransfer;
    }

    public function isOwnedBy(QuoteTransfer $quoteTransfer, CustomerTransfer $customerTransfer): bool
    {
        return $quoteTransfer->getCustomerReference() === $customerTransfer->getCustomerReference();
    }
}
