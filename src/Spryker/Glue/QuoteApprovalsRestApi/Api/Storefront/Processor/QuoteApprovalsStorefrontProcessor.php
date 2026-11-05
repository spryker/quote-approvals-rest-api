<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Processor;

use Generated\Api\Storefront\QuoteApprovalsStorefrontResource;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteApprovalRequestTransfer;
use Generated\Shared\Transfer\QuoteApprovalResponseTransfer;
use Generated\Shared\Transfer\QuoteApprovalTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCheckoutRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCustomerTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\QuoteApproval\QuoteApprovalClientInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Checker\QuoteApprovalRequestPermissionCheckerInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Exception\QuoteApprovalsExceptionFactory;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Mapper\QuoteApprovalResourceMapperInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CartReaderInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CheckoutQuoteReaderInterface;
use Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Reader\CompanyUserResolverInterface;

class QuoteApprovalsStorefrontProcessor extends AbstractStorefrontProcessor
{
    protected const string URI_VARIABLE_CART_UUID = 'cartUuid';

    public function __construct(
        protected QuoteApprovalClientInterface $quoteApprovalClient,
        protected CartReaderInterface $cartReader,
        protected CompanyUserResolverInterface $companyUserResolver,
        protected QuoteApprovalRequestPermissionCheckerInterface $quoteApprovalRequestPermissionChecker,
        protected CheckoutQuoteReaderInterface $checkoutQuoteReader,
        protected QuoteApprovalResourceMapperInterface $quoteApprovalResourceMapper,
        protected QuoteApprovalsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): QuoteApprovalsStorefrontResource
    {
        /** @var \Generated\Api\Storefront\QuoteApprovalsStorefrontResource $quoteApprovalsStorefrontResource */
        $quoteApprovalsStorefrontResource = $data;
        $customerTransfer = $this->getCustomer();
        $companyUserTransfer = $this->companyUserResolver->getCompanyUser($customerTransfer);

        $quoteTransfer = $this->cartReader->getOwnedOrSharedQuote(
            (string)$this->findUriVariable(static::URI_VARIABLE_CART_UUID),
            $customerTransfer,
        );

        if (!$this->cartReader->isOwnedBy($quoteTransfer, $customerTransfer)) {
            throw $this->exceptionFactory->createCartOwnerRequiredException();
        }

        $this->quoteApprovalRequestPermissionChecker->assertMayRequestQuoteApproval($companyUserTransfer);

        if ($quoteTransfer->getItems()->count() === 0) {
            throw $this->exceptionFactory->createEmptyCartException();
        }

        $approverCompanyUserTransfer = $this->getApproverFromApproverList(
            $quoteTransfer,
            (string)$quoteApprovalsStorefrontResource->getApproverCompanyUserUuid(),
        );

        $restCheckoutRequestAttributesTransfer = $this->quoteApprovalResourceMapper
            ->mapResourceToRestCheckoutRequestAttributesTransfer($quoteApprovalsStorefrontResource, new RestCheckoutRequestAttributesTransfer())
            ->setIdCart($quoteTransfer->getUuidOrFail())
            ->setCustomer($this->quoteApprovalResourceMapper->mapCompanyUserTransferToRestCustomerTransfer(
                $customerTransfer,
                $companyUserTransfer,
                new RestCustomerTransfer(),
            ));

        $quoteTransfer = $this->checkoutQuoteReader->getQuoteWithCheckoutData($restCheckoutRequestAttributesTransfer);

        $quoteApprovalResponseTransfer = $this->quoteApprovalClient->createQuoteApproval(
            (new QuoteApprovalRequestTransfer())
                ->setQuote($quoteTransfer)
                ->setApproverCompanyUserId($approverCompanyUserTransfer->getIdCompanyUserOrFail())
                ->setRequesterCompanyUserId($companyUserTransfer->getIdCompanyUserOrFail()),
        );

        if (!$quoteApprovalResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromQuoteApprovalResponse($quoteApprovalResponseTransfer);
        }

        return $this->quoteApprovalResourceMapper->mapQuoteApprovalTransferToResource(
            $this->getCreatedQuoteApproval($quoteApprovalResponseTransfer, $approverCompanyUserTransfer),
            new QuoteApprovalsStorefrontResource(),
            $quoteTransfer->getUuidOrFail(),
        );
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function getApproverFromApproverList(QuoteTransfer $quoteTransfer, string $approverCompanyUserUuid): CompanyUserTransfer
    {
        foreach ($this->quoteApprovalClient->getQuoteApproverList($quoteTransfer)->getCompanyUsers() as $approverCompanyUserTransfer) {
            if ($approverCompanyUserTransfer->getUuid() === $approverCompanyUserUuid) {
                return $approverCompanyUserTransfer;
            }
        }

        throw $this->exceptionFactory->createApproverNotAllowedException();
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function getCreatedQuoteApproval(
        QuoteApprovalResponseTransfer $quoteApprovalResponseTransfer,
        CompanyUserTransfer $approverCompanyUserTransfer,
    ): QuoteApprovalTransfer {
        $createdQuoteApprovalTransfer = null;

        foreach ($quoteApprovalResponseTransfer->getQuoteOrFail()->getQuoteApprovals() as $quoteApprovalTransfer) {
            if ($quoteApprovalTransfer->getApproverCompanyUserId() !== $approverCompanyUserTransfer->getIdCompanyUser()) {
                continue;
            }

            if ($createdQuoteApprovalTransfer === null || $quoteApprovalTransfer->getIdQuoteApproval() > $createdQuoteApprovalTransfer->getIdQuoteApproval()) {
                $createdQuoteApprovalTransfer = $quoteApprovalTransfer;
            }
        }

        if ($createdQuoteApprovalTransfer === null) {
            throw $this->exceptionFactory->createQuoteApprovalNotCreatedException();
        }

        return $createdQuoteApprovalTransfer;
    }
}
