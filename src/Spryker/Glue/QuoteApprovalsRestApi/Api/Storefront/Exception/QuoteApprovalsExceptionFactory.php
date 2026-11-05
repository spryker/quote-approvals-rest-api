<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi\Api\Storefront\Exception;

use Generated\Shared\Transfer\QuoteApprovalResponseTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\QuoteApprovalsRestApi\QuoteApprovalsRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

class QuoteApprovalsExceptionFactory
{
    public function __construct(
        protected QuoteApprovalsRestApiConfig $quoteApprovalsRestApiConfig,
    ) {
    }

    public function createCartNotFoundException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_NOT_FOUND,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND,
            QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_CART_NOT_FOUND,
        );
    }

    public function createCompanyUserNotSelectedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_COMPANY_USER_NOT_SELECTED,
            QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_COMPANY_USER_NOT_SELECTED,
        );
    }

    public function createRequestQuoteApprovalPermissionMissingException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_REQUEST_QUOTE_APPROVAL_PERMISSION_MISSING,
            QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_REQUEST_QUOTE_APPROVAL_PERMISSION_MISSING,
        );
    }

    public function createApproverNotAllowedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_APPROVER_NOT_ALLOWED,
            QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_APPROVER_NOT_ALLOWED,
        );
    }

    public function createCartNotReadyForApprovalException(?string $detail = null): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_CART_NOT_READY_FOR_APPROVAL,
            $detail ?: QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_CART_NOT_READY_FOR_APPROVAL,
        );
    }

    public function createQuoteApprovalNotFoundException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_NOT_FOUND,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_QUOTE_APPROVAL_NOT_FOUND,
            QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_QUOTE_APPROVAL_NOT_FOUND,
        );
    }

    public function createCartOwnerRequiredException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_CART_OWNER_REQUIRED,
            QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_CART_OWNER_REQUIRED,
        );
    }

    public function createQuoteApprovalCancelNotPermittedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            QuoteApprovalsRestApiConfig::RESPONSE_CODE_CART_OWNER_REQUIRED,
            QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_QUOTE_APPROVAL_CANCEL_NOT_PERMITTED,
        );
    }

    public function createEmptyCartException(): GlueApiException
    {
        return $this->createCartNotReadyForApprovalException(QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_EMPTY_CART);
    }

    public function createQuoteApprovalNotCreatedException(): GlueApiException
    {
        return $this->createCartNotReadyForApprovalException(QuoteApprovalsRestApiConfig::RESPONSE_DETAIL_QUOTE_APPROVAL_NOT_CREATED);
    }

    public function createExceptionFromQuoteApprovalResponse(QuoteApprovalResponseTransfer $quoteApprovalResponseTransfer): GlueApiException
    {
        $messageTransfer = $quoteApprovalResponseTransfer->getMessages()->getIterator()->current();
        $message = $messageTransfer?->getValue();
        $restError = $this->quoteApprovalsRestApiConfig->getErrorIdentifierToRestErrorMapping()[(string)$message] ?? null;

        if ($restError === null) {
            return $this->createCartNotReadyForApprovalException($message);
        }

        return new GlueApiException(
            $restError[RestErrorMessageTransfer::STATUS],
            $restError[RestErrorMessageTransfer::CODE],
            $restError[RestErrorMessageTransfer::DETAIL],
        );
    }
}
