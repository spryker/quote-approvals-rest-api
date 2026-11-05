<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Spryker\Client\CheckoutRestApi\CheckoutRestApiClientInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Exception\QuoteApprovalsExceptionFactory;

class CheckoutQuoteReader implements CheckoutQuoteReaderInterface
{
    public function __construct(
        protected CheckoutRestApiClientInterface $checkoutRestApiClient,
        protected QuoteApprovalsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function getQuoteWithCheckoutData(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): QuoteTransfer
    {
        $restCheckoutDataResponseTransfer = $this->checkoutRestApiClient->getCheckoutData($restCheckoutRequestAttributesTransfer);
        $checkoutQuoteTransfer = $restCheckoutDataResponseTransfer->getCheckoutData()?->getQuote();

        if (!$restCheckoutDataResponseTransfer->getIsSuccess() || $checkoutQuoteTransfer === null) {
            $restCheckoutErrorTransfer = $restCheckoutDataResponseTransfer->getErrors()->getIterator()->current();

            throw $this->exceptionFactory->createCartNotReadyForApprovalException($restCheckoutErrorTransfer?->getDetail());
        }

        return $checkoutQuoteTransfer;
    }
}
