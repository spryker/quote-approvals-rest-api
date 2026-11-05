<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\CartsStorefrontResource;
use Generated\Api\Storefront\QuoteApproversStorefrontResource;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\QuoteApproval\QuoteApprovalClientInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CartReaderInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CompanyUserResolverInterface;

class QuoteApproversStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string URI_VARIABLE_CART_UUID = 'cartUuid';

    public function __construct(
        protected QuoteApprovalClientInterface $quoteApprovalClient,
        protected CartReaderInterface $cartReader,
        protected CompanyUserResolverInterface $companyUserResolver,
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\QuoteApproversStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $customerTransfer = $this->getCustomer();
        $this->companyUserResolver->assertCompanyUserSelected($customerTransfer);

        $quoteTransfer = $this->cartReader->getOwnedQuote(
            (string)$this->findUriVariable(static::URI_VARIABLE_CART_UUID),
            $customerTransfer,
        );

        $cartsStorefrontResource = (new CartsStorefrontResource())->setUuid($quoteTransfer->getUuidOrFail());
        $resources = [];

        foreach ($this->quoteApprovalClient->getQuoteApproverList($quoteTransfer)->getCompanyUsers() as $companyUserTransfer) {
            $resources[] = $this->mapCompanyUserToResource($companyUserTransfer, $cartsStorefrontResource);
        }

        return $resources;
    }

    protected function mapCompanyUserToResource(
        CompanyUserTransfer $companyUserTransfer,
        CartsStorefrontResource $cartsStorefrontResource,
    ): QuoteApproversStorefrontResource {
        return (new QuoteApproversStorefrontResource())
            ->setCompanyUserUuid($companyUserTransfer->getUuid())
            ->setCart($cartsStorefrontResource)
            ->setFirstName($companyUserTransfer->getCustomer()?->getFirstName())
            ->setLastName($companyUserTransfer->getCustomer()?->getLastName())
            ->setCompanyBusinessUnitName($companyUserTransfer->getCompanyBusinessUnit()?->getName());
    }
}
