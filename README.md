# QuoteApprovalsRestApi Module
[![Latest Stable Version](https://poser.pugx.org/spryker/quote-approvals-rest-api/v/stable.svg)](https://packagist.org/packages/spryker/quote-approvals-rest-api)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.3-8892BF.svg)](https://php.net/)

Lets an authenticated company user list the possible approvers of their cart, send the cart for approval, read the approval status and cancel a pending request through Storefront API Platform resources. All approval logic stays in `spryker/quote-approval`.

## Installation

```
composer require spryker/quote-approvals-rest-api
```

## How it works

### Resource discovery

API Platform reads every `*.resource.yml` under `resources/api/storefront/` of each module in the configured source directories (`spryker_api_platform.source_directories`, by default `vendor/spryker` and `src/Pyz`). No bundle, dependency provider or other project configuration registers this module. After a resource file changes:

```
GLUE_APPLICATION=GLUE_STOREFRONT vendor/bin/glue api:generate storefront
GLUE_APPLICATION=GLUE_STOREFRONT vendor/bin/glue cache:clear
```

### Resources

| Operation | What it does |
|---|---|
| `GET /carts/{cartUuid}/quote-approvers` | The company users of the caller's business unit who may approve carts. |
| `POST /carts/{cartUuid}/quote-approvals` | Sends the cart to one of those approvers (`approverCompanyUserUuid`) together with its checkout data; answers `201` with the created approval, and the cart is locked. |
| `GET /carts/{cartUuid}/quote-approvals` | The cart's approvals, with the cart's approval status (`waiting`, `approved`, `declined`, or `null` when none is requested or it was cancelled) in `meta.cartApprovalStatus`. |
| `POST /carts/{cartUuid}/quote-approvals/{quoteApprovalUuid}/cancel` | Cancels the pending approval of the cart: answers `204` without a body, the approval is removed and the cart is unlocked. |

Reads answer only for the caller's own cart. A request for approval of a cart shared with the caller is refused with `5756` right after the cart is read, before the permission, approver and checkout data checks; any other cart is not found.

Cancelling is for the requester only. The approval id is checked against the approvals of the cart first (`5755`), because the quote approval module fails on an id it does not know. A cart shared with the caller, which is what the approver sees after a request, is found like for the request and refused with `5756`: the quote approval validation would also let the approver withdraw, which this resource does not offer. A cart that is neither owned nor shared answers `101`.

The three `Get` operations that sit next to the `Post` operations in the resource files (`/carts/{cartUuid}/quote-approvals/{quoteApprovalUuid}`, `/carts/{cartUuid}/quote-approvers/{companyUserUuid}` and the `/cancel` path) are internal anchors that answer `404`: API Platform needs an item operation to mint the IRI of a resource, and would otherwise register a public route of its own.

### Scope of delegated access

Every operation that changes a cart carries the security expression

```
is_granted('ROLE_CUSTOMER') and (not is_granted('ROLE_AGENTIC_COMMERCE') or is_granted('ROLE_SCOPE_CART_WRITE'))
```

and the two reads the same with `ROLE_SCOPE_CART_READ`. A customer's own token passes as before. A token of delegated access carries `ROLE_AGENTIC_COMMERCE` and the roles of the scopes it was granted (`ROLE_SCOPE_CART_WRITE` for `cart:write`), so it needs the scope. The roles are plain strings in the expression, so this module has no dependency on the AgenticCommerce feature and works unchanged in a project without it.

### Checkout data of an approval request

The quote approval check requires the fields of `QuoteApprovalConfig::getRequiredQuoteFieldsForApprovalProcess()` (for example the billing address and the payment) and the `QuoteApplicableForApprovalCheckPluginInterface` plugins (for example shipments), while a persistent cart stores only the fields of `QuoteConfig::getQuoteFieldsAllowedForSaving()`. So a request for approval carries `billingAddress`, `shippingAddress`, `shipment` and `payments` like the `checkout` resource. `CheckoutRestApiClientInterface::getCheckoutData()` applies them to the cart through the project's quote mapper plugins and returns the recalculated quote without saving or placing it; that quote is passed to `QuoteApprovalClientInterface::createQuoteApproval()`. Once the approval request succeeds, the quote approval module stores the checkout data (shipping and billing address, shipments, shipment expense and payment) with the locked cart, so the approver sees a cart ready for approval; on any refusal nothing is written.

### Permission to request an approval

`PermissionClientInterface::can()` reads permissions from the customer session and from the Authorization header of the PHP globals, neither of which a stateless API request provides reliably. The module reads the company user's roles with `CompanyRoleClientInterface::getCompanyRoleCollection()` (filter `idCompanyUser`), whose roles carry their permission collections, and checks for `RequestQuoteApprovalPermissionPlugin::KEY`.

### Error codes

| Code | Status | Detail |
|---|---|---|
| 101 | 404 | `Cart with given uuid not found.` The cart does not exist or does not belong to the caller (for a request for approval and for a cancellation: is neither owned by nor shared with the caller). |
| 1403 | 403 | `Current company user is not set. You need to select the current company user with /company-user-access-tokens in order to access the resource.` The caller is not a company user. |
| 5751 | 403 | `The company user is not allowed to request approval of a cart.` |
| 5752 | 422 | `The approver is not allowed to approve this cart.` |
| 5753 | 422 | `The cart is already waiting for approval.` or `The cart is already approved.` |
| 5754 | 422 | `An empty cart cannot be sent for approval.`, `The cart cannot be sent for approval without a billing address, a payment and a shipment.` or `The approval of the cart could not be requested.`; an unknown refusal of the quote approval module answers it too, with its message as detail. |
| 5755 | 404 | `Quote approval with given uuid not found in the cart.` |
| 5756 | 403 | `Only the owner of the cart can send it for approval.` (request) or `Only the requester of the approval can cancel it.` (cancel) |
| 901 | 422 | The validation messages of the request. |

The details are English constants of `QuoteApprovalsRestApiConfig`, not glossary keys.

## Documentation

[Spryker Documentation](https://docs.spryker.com)
