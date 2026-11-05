<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\QuoteApprovalsRestApi;

use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\Kernel\AbstractBundleConfig;
use Symfony\Component\HttpFoundation\Response;

class QuoteApprovalsRestApiConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @uses \Spryker\Glue\CartsRestApi\CartsRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND
     */
    public const string RESPONSE_CODE_CART_NOT_FOUND = '101';

    /**
     * @api
     *
     * @uses \Spryker\Glue\CompanyUsersRestApi\CompanyUsersRestApiConfig::RESPONSE_CODE_COMPANY_USER_NOT_SELECTED
     */
    public const string RESPONSE_CODE_COMPANY_USER_NOT_SELECTED = '1403';

    /**
     * @api
     *
     * @uses \Spryker\Glue\CartsRestApi\CartsRestApiConfig::EXCEPTION_MESSAGE_CART_WITH_ID_NOT_FOUND
     */
    public const string RESPONSE_DETAIL_CART_NOT_FOUND = 'Cart with given uuid not found.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_COMPANY_USER_NOT_SELECTED = 'Current company user is not set. You need to select the current company user with /company-user-access-tokens in order to access the resource.';

    /**
     * @api
     */
    public const string RESPONSE_CODE_REQUEST_QUOTE_APPROVAL_PERMISSION_MISSING = '5751';

    /**
     * @api
     */
    public const string RESPONSE_CODE_APPROVER_NOT_ALLOWED = '5752';

    /**
     * @api
     */
    public const string RESPONSE_CODE_APPROVAL_ALREADY_REQUESTED = '5753';

    /**
     * @api
     */
    public const string RESPONSE_CODE_CART_NOT_READY_FOR_APPROVAL = '5754';

    /**
     * @api
     */
    public const string RESPONSE_CODE_QUOTE_APPROVAL_NOT_FOUND = '5755';

    /**
     * @api
     */
    public const string RESPONSE_CODE_CART_OWNER_REQUIRED = '5756';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_REQUEST_QUOTE_APPROVAL_PERMISSION_MISSING = 'The company user is not allowed to request approval of a cart.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_APPROVER_NOT_ALLOWED = 'The approver is not allowed to approve this cart.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_QUOTE_ALREADY_WAITING_FOR_APPROVAL = 'The cart is already waiting for approval.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_QUOTE_ALREADY_APPROVED = 'The cart is already approved.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_EMPTY_CART = 'An empty cart cannot be sent for approval.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_CART_NOT_READY_FOR_APPROVAL = 'The cart cannot be sent for approval without a billing address, a payment and a shipment.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_QUOTE_APPROVAL_NOT_CREATED = 'The approval of the cart could not be requested.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_QUOTE_APPROVAL_NOT_FOUND = 'Quote approval with given uuid not found in the cart.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_QUOTE_APPROVAL_CANCEL_NOT_PERMITTED = 'Only the requester of the approval can cancel it.';

    /**
     * @api
     */
    public const string RESPONSE_DETAIL_CART_OWNER_REQUIRED = 'Only the owner of the cart can send it for approval.';

    /**
     * @uses \Spryker\Zed\QuoteApproval\Business\QuoteApproval\QuoteApprovalRequestValidator::GLOSSARY_KEY_CANT_SEND_FOR_APPROVE_EMPTY_QUOTE
     */
    protected const string GLOSSARY_KEY_CANT_SEND_FOR_APPROVE_EMPTY_QUOTE = 'quote_approval.create.cant_send_for_approve_empty_quote';

    /**
     * @uses \Spryker\Zed\QuoteApproval\Business\QuoteApproval\QuoteApprovalRequestValidator::GLOSSARY_KEY_ONLY_QUOTE_OWNER_CAN_SEND_APPROVAL_REQUEST
     */
    protected const string GLOSSARY_KEY_ONLY_QUOTE_OWNER_CAN_SEND_APPROVAL_REQUEST = 'quote_approval.create.only_quote_owner_can_send_request';

    /**
     * @uses \Spryker\Zed\QuoteApproval\Business\QuoteApproval\QuoteApprovalRequestValidator::GLOSSARY_KEY_APPROVER_CANT_APPROVE_QUOTE
     */
    protected const string GLOSSARY_KEY_APPROVER_CANT_APPROVE_QUOTE = 'quote_approval.create.approver_cant_approve_quote';

    /**
     * @uses \Spryker\Zed\QuoteApproval\Business\QuoteApproval\QuoteApprovalRequestValidator::GLOSSARY_KEY_QUOTE_ALREADY_WAITING_FOR_APPROVAL
     */
    protected const string GLOSSARY_KEY_QUOTE_ALREADY_WAITING_FOR_APPROVAL = 'quote_approval.create.quote_already_waiting_for_approval';

    /**
     * @uses \Spryker\Zed\QuoteApproval\Business\QuoteApproval\QuoteApprovalRequestValidator::GLOSSARY_KEY_QUOTE_ALREADY_APPROVED
     */
    protected const string GLOSSARY_KEY_QUOTE_ALREADY_APPROVED = 'quote_approval.create.quote_already_approved';

    /**
     * @uses \Spryker\Zed\QuoteApproval\Business\QuoteApproval\QuoteApprovalRequestValidator::GLOSSARY_KEY_DO_NOT_HAVE_PERMISSION_TO_CANCEL_APPROVAL_REQUEST
     */
    protected const string GLOSSARY_KEY_DO_NOT_HAVE_PERMISSION_TO_CANCEL_APPROVAL_REQUEST = 'quote_approval.cancel.do_not_have_permission';

    /**
     * @uses \Spryker\Client\QuoteApproval\QuoteApproval\QuoteApprovalCreator::GLOSSARY_KEY_CART_CANT_BE_SENT_FOR_APPROVAL
     */
    protected const string GLOSSARY_KEY_CART_CANT_BE_SENT_FOR_APPROVAL = 'quote_approval.create.cart_cant_be_sent_for_approval';

    /**
     * Specification:
     * - Maps the glossary keys of the quote approval Client and Zed validators to the code, status
     *   and detail of the error the API answers with.
     *
     * @api
     *
     * @return array<string, array<string, mixed>>
     */
    public function getErrorIdentifierToRestErrorMapping(): array
    {
        return [
            static::GLOSSARY_KEY_QUOTE_ALREADY_WAITING_FOR_APPROVAL => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_APPROVAL_ALREADY_REQUESTED,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::RESPONSE_DETAIL_QUOTE_ALREADY_WAITING_FOR_APPROVAL,
            ],
            static::GLOSSARY_KEY_QUOTE_ALREADY_APPROVED => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_APPROVAL_ALREADY_REQUESTED,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::RESPONSE_DETAIL_QUOTE_ALREADY_APPROVED,
            ],
            static::GLOSSARY_KEY_CANT_SEND_FOR_APPROVE_EMPTY_QUOTE => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CART_NOT_READY_FOR_APPROVAL,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::RESPONSE_DETAIL_EMPTY_CART,
            ],
            static::GLOSSARY_KEY_CART_CANT_BE_SENT_FOR_APPROVAL => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CART_NOT_READY_FOR_APPROVAL,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::RESPONSE_DETAIL_CART_NOT_READY_FOR_APPROVAL,
            ],
            static::GLOSSARY_KEY_ONLY_QUOTE_OWNER_CAN_SEND_APPROVAL_REQUEST => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CART_OWNER_REQUIRED,
                RestErrorMessageTransfer::STATUS => Response::HTTP_FORBIDDEN,
                RestErrorMessageTransfer::DETAIL => static::RESPONSE_DETAIL_CART_OWNER_REQUIRED,
            ],
            static::GLOSSARY_KEY_DO_NOT_HAVE_PERMISSION_TO_CANCEL_APPROVAL_REQUEST => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_CART_OWNER_REQUIRED,
                RestErrorMessageTransfer::STATUS => Response::HTTP_FORBIDDEN,
                RestErrorMessageTransfer::DETAIL => static::RESPONSE_DETAIL_QUOTE_APPROVAL_CANCEL_NOT_PERMITTED,
            ],
            static::GLOSSARY_KEY_APPROVER_CANT_APPROVE_QUOTE => [
                RestErrorMessageTransfer::CODE => static::RESPONSE_CODE_APPROVER_NOT_ALLOWED,
                RestErrorMessageTransfer::STATUS => Response::HTTP_UNPROCESSABLE_ENTITY,
                RestErrorMessageTransfer::DETAIL => static::RESPONSE_DETAIL_APPROVER_NOT_ALLOWED,
            ],
        ];
    }
}
