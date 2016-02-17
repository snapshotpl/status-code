# status-code
HTTP status code value object implementation in PHP.

Features:
* validation,
* auto setup reason phrase (if known),
* immutable,
* support PSR-7 `Psr\Http\Message\ResponseInterface`.

Supported RFCs:
* https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
* https://tools.ietf.org/html/rfc4918
* https://tools.ietf.org/html/rfc3229
* https://tools.ietf.org/html/rfc5842
* https://tools.ietf.org/html/rfc7538
* https://tools.ietf.org/html/rfc7540
* https://tools.ietf.org/html/rfc7231
* https://tools.ietf.org/html/rfc2518
* https://tools.ietf.org/html/rfc6585
* https://tools.ietf.org/html/rfc2295
* https://tools.ietf.org/html/rfc2774

Supported drafts:
* https://tools.ietf.org/html/draft-ietf-httpbis-legally-restricted-status-04