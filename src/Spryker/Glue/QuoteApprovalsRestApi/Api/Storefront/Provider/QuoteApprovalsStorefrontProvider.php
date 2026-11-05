<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\QuoteApprovalsStorefrontResource;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\QuoteApproval\QuoteApprovalClientInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Mapper\QuoteApprovalResourceMapperInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CartReaderInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CompanyUserResolverInterface;

class QuoteApprovalsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string URI_VARIABLE_CART_UUID = 'cartUuid';

    protected const string META_CART_APPROVAL_STATUS = 'cartApprovalStatus';

    public function __construct(
        protected QuoteApprovalClientInterface $quoteApprovalClient,
        protected CartReaderInterface $cartReader,
        protected CompanyUserResolverInterface $companyUserResolver,
        protected QuoteApprovalResourceMapperInterface $quoteApprovalResourceMapper,
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\QuoteApprovalsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $customerTransfer = $this->getCustomer();
        $this->companyUserResolver->assertCompanyUserSelected($customerTransfer);

        $quoteTransfer = $this->cartReader->getOwnedQuote(
            (string)$this->findUriVariable(static::URI_VARIABLE_CART_UUID),
            $customerTransfer,
        );

        $this->addCollectionMeta(static::META_CART_APPROVAL_STATUS, $this->quoteApprovalClient->calculateQuoteStatus($quoteTransfer));

        $resources = [];

        foreach ($quoteTransfer->getQuoteApprovals() as $quoteApprovalTransfer) {
            $resources[] = $this->quoteApprovalResourceMapper->mapQuoteApprovalTransferToResource(
                $quoteApprovalTransfer,
                new QuoteApprovalsStorefrontResource(),
                $quoteTransfer->getUuidOrFail(),
            );
        }

        return $resources;
    }
}
