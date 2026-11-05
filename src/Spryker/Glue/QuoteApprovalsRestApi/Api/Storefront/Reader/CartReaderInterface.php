<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface CartReaderInterface
{
    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function getOwnedQuote(string $cartUuid, CustomerTransfer $customerTransfer): QuoteTransfer;

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function getOwnedOrSharedQuote(string $cartUuid, CustomerTransfer $customerTransfer): QuoteTransfer;

    public function isOwnedBy(QuoteTransfer $quoteTransfer, CustomerTransfer $customerTransfer): bool;
}
