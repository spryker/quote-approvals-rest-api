<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;

interface CheckoutQuoteReaderInterface
{
    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    public function getQuoteWithCheckoutData(RestCheckoutRequestAttributesTransfer $restCheckoutRequestAttributesTransfer): QuoteTransfer;
}
