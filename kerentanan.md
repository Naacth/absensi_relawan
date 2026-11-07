# ZAP by Checkmarx Scanning Report

- **Sites:** https://edge-consumer-static.azureedge.net, https://cdn.jsdelivr.net, http://127.0.0.1:8000
- **Generated on:** Sun, 8 Nov 2026 00:39:50
- **ZAP Version:** 2.17.0

> Catatan: file ini dikonversi dari PDF hasil "Print to PDF" (tanpa teks asli) menggunakan OCR, lalu dirapikan. Nilai cookie/sesi pada bagian *Modern Web Application* sengaja disensor (`[REDACTED]`).

---

## Summary of Alerts

| Risk Level    | Number of Alerts |
|---------------|------------------|
| High          | 1 |
| Medium        | 4 |
| Low           | 6 |
| Informational | 5 |

## Insights

| Level | Reason | Site | Description | Statistic |
|-------|--------|------|-------------|-----------|
| Low | Warning | | ZAP errors logged - see the zap.log file for details | 2 |
| Info | Informational | | Percentage of network failures | 1 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of responses with status code 2xx | 86 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of responses with status code 3xx | 10 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of responses with status code 4xx | 3 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of endpoints with content type application/pdf | 6 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of endpoints with content type image/vnd.microsoft.icon | 6 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of endpoints with content type text/html | 86 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of endpoints with method GET | 80 % |
| Info | Informational | http://127.0.0.1:8000 | Percentage of endpoints with method POST | 20 % |
| Info | Informational | http://127.0.0.1:8000 | Count of total endpoints | 15 |
| Info | Informational | http://127.0.0.1:8000 | Percentage of slow responses | 56 % |
| Info | Informational | https://cdn.jsdelivr.net | Percentage of responses with status code 2xx | 100 % |
| Info | Informational | https://cdn.jsdelivr.net | Percentage of endpoints with content type application/javascript | 40 % |
| Info | Informational | https://cdn.jsdelivr.net | Percentage of endpoints with content type font/woff2 | 20 % |
| Info | Informational | https://cdn.jsdelivr.net | Percentage of endpoints with content type text/css | 40 % |
| Info | Informational | https://cdn.jsdelivr.net | Percentage of endpoints with method GET | 100 % |
| Info | Informational | https://cdn.jsdelivr.net | Count of total endpoints | 5 |
| Info | Informational | https://cdn.jsdelivr.net | Percentage of slow responses | 40 % |
| Info | Informational | https://edge-consumer-static.azureedge.net | Percentage of responses with status code 2xx | 100 % |
| Info | Informational | https://edge-consumer-static.azureedge.net | Percentage of endpoints with content type application/json | 100 % |
| Info | Informational | https://edge-consumer-static.azureedge.net | Percentage of endpoints with method GET | 100 % |
| Info | Informational | https://edge-consumer-static.azureedge.net | Count of total endpoints | 1 |

## Summary of Sequences

For each step: result (Pass/Fail) - risk (of highest alert(s) for the step, if any).

## Alerts

| Name | Risk Level | Number of Instances |
|------|------------|---------------------|
| PHP Version with Known Vulnerabilities | High | 1 |
| Content Security Policy (CSP) Header Not Set | Medium | Systemic |
| Cross-Domain Misconfiguration | Medium | 6 |
| Missing Anti-clickjacking Header | Medium | Systemic |
| Sub Resource Integrity Attribute Missing | Medium | Systemic |
| Big Redirect Detected (Potential Sensitive Information Leak) | Low | 2 |
| Cookie No HttpOnly Flag | Low | Systemic |
| Cross-Domain JavaScript Source File Inclusion | Low | Systemic |
| Server Leaks Information via "X-Powered-By" HTTP Response Header Field(s) | Low | Systemic |
| Strict-Transport-Security Header Not Set | Low | 1 |
| X-Content-Type-Options Header Missing | Low | Systemic |
| Authentication Request Identified | Informational | 1 |
| Modern Web Application | Informational | 1 |
| Re-examine Cache-control Directives | Informational | 1 |
| Retrieved from Cache | Informational | 5 |
| Session Management Response Identified | Informational | 13 |

---

## Alert Detail

### High — PHP Version with Known Vulnerabilities

**Description**
The server disclosed the version of a server side technology it is running via an HTTP response header, and that version is affected by known vulnerabilities. The version, header and vulnerabilities, listed by severity, are given in the other info. See the references for the details of each.

Note that some distributions backport security fixes without changing the version number, so check whether the fixes have actually been applied before reporting these as confirmed.

- **URL:** http://127.0.0.1:8000/login
- **Method:** GET
- **Evidence:** `PHP/8.2.12`
- **Other Info:** The server disclosed that it is running PHP version 8.2.12 via the "X-Powered-By" HTTP response header. 56 known vulnerabilities: 3 critical, 10 high, 36 medium, 7 low.
  - **Critical:** CVE-2024-1874, CVE-2026-109618, CVE-2026-109619
  - **High:** CVE-2024-2757, CVE-2024-4577, CVE-2024-8929, CVE-2025-14179, CVE-2026-12184, CVE-2026-17543, CVE-2026-17544, CVE-2026-6722, CVE-2026-7263, CVE-2026-91765
  - **Medium:** CVE-2024-11233, CVE-2024-11234, CVE-2024-11235, CVE-2024-11236, CVE-2024-2408, CVE-2024-2756, CVE-2024-5458, CVE-2024-8926, CVE-2024-8927, CVE-2024-8932, CVE-2025-1217, CVE-2025-1219, CVE-2025-14177, CVE-2025-14178, CVE-2025-14180, CVE-2025-14181, CVE-2025-1734, CVE-2025-1735, CVE-2025-1736, CVE-2025-1861, CVE-2025-6491, CVE-2026-14355, CVE-2026-17545, CVE-2026-6103, CVE-2026-6104, CVE-2026-6735, CVE-2026-7258, CVE-2026-7260, CVE-2026-7261, CVE-2026-7262, CVE-2026-91766, CVE-2026-91767, CVE-2026-91768, CVE-2026-91769, CVE-2026-92842, CVE-2026-93682
  - **Low:** CVE-2024-3096, CVE-2024-8925, CVE-2024-9026, CVE-2025-1218, CVE-2025-1220, CVE-2026-7259, CVE-2026-7568
- **Instances:** 1

**Solution**
Upgrade PHP to a supported version which includes fixes for the listed vulnerabilities. Also configure the server not to disclose the PHP version, e.g. by setting `expose_php = Off` in php.ini and suppressing version details in the "Server" header.

**Reference**
- https://www.php.net/supported-versions.php
- https://github.com/php/php-src/security/advisories
- https://www.zaproxy.org/docs/desktop/addons/passive-scan-rules/php-known-vulnerabilities/ (anchor per CVE, e.g. `#cve-2024-4577`)

CWE Id: 1395 · WASC Id: 13 · Plugin Id: 20020

---

### Medium — Content Security Policy (CSP) Header Not Set

**Description**
Content Security Policy (CSP) is an added layer of security that helps to detect and mitigate certain types of attacks, including Cross Site Scripting (XSS) and data injection attacks. These attacks are used for everything from data theft to site defacement or distribution of malware. CSP provides a set of standard HTTP headers that allow website owners to declare approved sources of content that browsers should be allowed to load on that page — covered types are JavaScript, CSS, HTML frames, fonts, images and embeddable objects such as Java applets, ActiveX, audio and video files.

**Affected URLs (GET)**
- http://127.0.0.1:8000/admin/dashboard
- http://127.0.0.1:8000/admin/divisions
- http://127.0.0.1:8000/admin/divisions/1/edit
- http://127.0.0.1:8000/admin/relawan
- http://127.0.0.1:8000/login

**Instances:** Systemic

**Solution**
Ensure that your web server, application server, load balancer, etc. is configured to set the Content-Security-Policy header.

**Reference**
- https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CSP
- https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html
- https://www.w3.org/TR/CSP/
- https://web.dev/articles/csp
- https://caniuse.com/#feat=contentsecuritypolicy
- https://content-security-policy.com/

CWE Id: 693 · WASC Id: 15 · Plugin Id: 10038

---

### Medium — Cross-Domain Misconfiguration

**Description**
Web browser data loading may be possible, due to a Cross Origin Resource Sharing (CORS) misconfiguration on the web server.

**Affected URLs (GET)** — Evidence: `access-control-allow-origin: *`
- https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css
- https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6
- https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
- https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js
- https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js
- https://edge-consumer-static.azureedge.net/mouse-gesture/config.json

**Other Info (same for every instance)**
The CORS misconfiguration on the web server permits cross-domain read requests from arbitrary third party domains, using unauthenticated APIs on this domain. Web browser implementations do not permit arbitrary third parties to read the response from authenticated APIs, however. This reduces the risk somewhat. This misconfiguration could be used by an attacker to access data that is available in an unauthenticated manner, but which uses some other form of security, such as IP address white-listing.

**Instances:** 6

**Solution**
Ensure that sensitive data is not available in an unauthenticated manner (using IP address white-listing, for instance).

Configure the "Access-Control-Allow-Origin" HTTP header to a more restrictive set of domains, or remove all CORS headers entirely, to allow the web browser to enforce the Same Origin Policy (SOP) in a more restrictive manner.

**Reference**
- https://vulncat.fortify.com/en/detail?category=HTML5&subcategory=Overly%20Permissive%20CORS%20Policy

CWE Id: 264 · WASC Id: 14 · Plugin Id: 10098

---

### Medium — Missing Anti-clickjacking Header

**Description**
The response does not protect against 'ClickJacking' attacks. It should include either Content-Security-Policy with 'frame-ancestors' directive or X-Frame-Options.

**Affected URLs (GET)** — Parameter: `x-frame-options`
- http://127.0.0.1:8000/admin/dashboard
- http://127.0.0.1:8000/admin/divisions
- http://127.0.0.1:8000/admin/divisions/1/edit
- http://127.0.0.1:8000/admin/relawan
- http://127.0.0.1:8000/login

**Instances:** Systemic

**Solution**
Modern Web browsers support the Content-Security-Policy and X-Frame-Options HTTP headers. Ensure one of them is set on all web pages returned by your site/app.

If you expect the page to be framed only by pages on your server (e.g. it's part of a FRAMESET) then you'll want to use SAMEORIGIN, otherwise if you never expect the page to be framed, you should use DENY. Alternatively consider implementing Content Security Policy's "frame-ancestors" directive.

**Reference**
- https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/X-Frame-Options

CWE Id: 1021 · WASC Id: 15 · Plugin Id: 10020

---

### Medium — Sub Resource Integrity Attribute Missing

**Description**
The integrity attribute is missing on a script or link tag served by an external server. The integrity tag prevents an attacker who have gained access to this server from injecting a malicious content.

**Affected URLs (GET)**

| URL | Evidence |
|-----|----------|
| http://127.0.0.1:8000/admin/dashboard | `<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">` |
| http://127.0.0.1:8000/admin/dashboard | `<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">` |
| http://127.0.0.1:8000/admin/dashboard | `<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>` |
| http://127.0.0.1:8000/login | `<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">` |
| http://127.0.0.1:8000/login | `<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">` |

**Other Info (script `bootstrap.bundle.min.js`):** the SHA-384 hash calculated for the script is `sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz` *(hasil OCR, verifikasi ulang sebelum dipakai)*.

**Instances:** Systemic

**Solution**
Provide a valid integrity attribute to the tag.

**Reference**
- https://developer.mozilla.org/en-US/docs/Web/Security/Defenses/Subresource_Integrity

CWE Id: 345 · WASC Id: 15 · Plugin Id: 90003

---

### Low — Big Redirect Detected (Potential Sensitive Information Leak)

**Description**
The server has responded with a redirect that seems to provide a large response. This may indicate that although the server sent a redirect it also responded with body content (which may include sensitive details, PII, etc.).

| URL | Method | Parameters | Other Info |
|-----|--------|-----------|------------|
| http://127.0.0.1:8000/admin/divisions/1 | POST | `_method, _token, check_in_end, check_in_start, check_out_end, check_out_start, daily_rate, daily_transport, description, late_deduction, late_tolerance, meal_allowance, name, overtime_hourly_rate` | Location header URI length: 37 [http://127.0.0.1:8000/admin/divisions]. Predicted response size: 337. Response Body Length: 394. |
| http://127.0.0.1:8000/login | POST | `_token, email, password` | Location header URI length: 27 [http://127.0.0.1:8000/login]. Predicted response size: 327. Response Body Length: 354. |

**Instances:** 2

**Solution**
Ensure that no sensitive information is leaked via redirect responses. Redirect responses should have almost no content.

CWE Id: 201 · WASC Id: 13 · Plugin Id: 10044

---

### Low — Cookie No HttpOnly Flag

**Description**
A cookie has been set without the HttpOnly flag, which means that the cookie can be accessed by JavaScript. If a malicious script can be run on this page then the cookie will be accessible and can be transmitted to another site. If this is a session cookie then session hijacking may be possible.

**Affected URLs** — Parameter: `XSRF-TOKEN`, Evidence: `Set-Cookie: XSRF-TOKEN`
- GET http://127.0.0.1:8000/admin/dashboard
- GET http://127.0.0.1:8000/admin/divisions
- GET http://127.0.0.1:8000/admin/relawan
- GET http://127.0.0.1:8000/login
- POST http://127.0.0.1:8000/login (`_token, email, password`)

**Instances:** Systemic

**Solution**
Ensure that the HttpOnly flag is set for all cookies.

**Reference**
- https://owasp.org/www-community/HttpOnly

CWE Id: 1004 · WASC Id: 13 · Plugin Id: 10010

---

### Low — Cross-Domain JavaScript Source File Inclusion

**Description**
The page includes one or more script files from a third-party domain.

| URL (GET) | Script (Parameter) |
|-----------|--------------------|
| http://127.0.0.1:8000/admin/dashboard | https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js |
| http://127.0.0.1:8000/admin/dashboard | https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js |
| http://127.0.0.1:8000/admin/divisions | https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js |
| http://127.0.0.1:8000/admin/divisions/1/edit | https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js |
| http://127.0.0.1:8000/admin/relawan | https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js |

**Instances:** Systemic

**Solution**
Ensure JavaScript source files are loaded from only trusted sources, and the sources can't be controlled by end users of the application.

CWE Id: 829 · WASC Id: 15 · Plugin Id: 10017

---

### Low — Server Leaks Information via "X-Powered-By" HTTP Response Header Field(s)

**Description**
The web/application server is leaking information via one or more "X-Powered-By" HTTP response headers. Access to such information may facilitate attackers identifying other frameworks/components your web application is reliant upon and the vulnerabilities such components may be subject to.

**Affected URLs** — Evidence: `X-Powered-By: PHP/8.2.12`
- GET http://127.0.0.1:8000/admin/dashboard
- GET http://127.0.0.1:8000/admin/divisions
- GET http://127.0.0.1:8000/admin/relawan
- GET http://127.0.0.1:8000/login
- POST http://127.0.0.1:8000/login (`_token, email, password`)

**Instances:** Systemic

**Solution**
Ensure that your web server, application server, load balancer, etc. is configured to suppress "X-Powered-By" headers.

**Reference**
- https://owasp.org/www-project-web-security-testing-guide/v42/4-Web_Application_Security_Testing/01-Information_Gathering/08-Fingerprint_Web_Application_Framework
- https://www.troyhunt.com/shhh-dont-let-your-response-headers/

CWE Id: 497 · WASC Id: 13 · Plugin Id: 10037

---

### Low — Strict-Transport-Security Header Not Set

**Description**
HTTP Strict Transport Security (HSTS) is a web security policy mechanism whereby a web server declares that complying user agents (such as a web browser) are to interact with it using only secure HTTPS connections (i.e. HTTP layered over TLS/SSL). HSTS is an IETF standards track protocol and is specified in RFC 6797.

- **URL:** https://edge-consumer-static.azureedge.net/mouse-gesture/config.json (GET)
- **Instances:** 1

**Solution**
Ensure that your web server, application server, load balancer, etc. is configured to enforce Strict-Transport-Security.

**Reference**
- https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Strict_Transport_Security_Cheat_Sheet.html
- https://owasp.org/www-community/Security_Headers
- https://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security
- https://caniuse.com/stricttransportsecurity
- https://datatracker.ietf.org/doc/html/rfc6797

CWE Id: 319 · WASC Id: 15 · Plugin Id: 10035

---

### Low — X-Content-Type-Options Header Missing

**Description**
The Anti-MIME-Sniffing header X-Content-Type-Options was not set to 'nosniff'. This allows older versions of Internet Explorer and Chrome to perform MIME-sniffing on the response body, potentially causing the response body to be interpreted and displayed as a content type other than the declared content type. Current (early 2014) and legacy versions of Firefox will use the declared content type (if one is set), rather than performing MIME-sniffing.

**Affected URLs (GET)** — Parameter: `x-content-type-options`
- http://127.0.0.1:8000/admin/dashboard
- http://127.0.0.1:8000/admin/divisions
- http://127.0.0.1:8000/admin/divisions/1/edit
- http://127.0.0.1:8000/admin/relawan
- http://127.0.0.1:8000/login
- https://edge-consumer-static.azureedge.net/mouse-gesture/config.json

**Other Info (same for every instance)**
This issue still applies to error type pages (401, 403, 500, etc.) as those pages are often still affected by injection issues, in which case there is still concern for browsers sniffing pages away from their actual content type. At "High" threshold this scan rule will not alert on client or server error responses.

**Instances:** Systemic

**Solution**
Ensure that the application/web server sets the Content-Type header appropriately, and that it sets the X-Content-Type-Options header to 'nosniff' for all web pages.

If possible, ensure that the end user uses a standards-compliant and modern web browser that does not perform MIME-sniffing at all, or that can be directed by the web application/web server to not perform MIME-sniffing.

**Reference**
- https://learn.microsoft.com/en-us/previous-versions/windows/internet-explorer/ie-developer/compatibility/gg622941(v=vs.85)
- https://owasp.org/www-community/Security_Headers

CWE Id: 693 · WASC Id: 15 · Plugin Id: 10021

---

### Informational — Authentication Request Identified

**Description**
The given request has been identified as an authentication request. The 'Other Info' field contains a set of key=value lines which identify any relevant fields. If the request is in a context which has an Authentication Method set to "Auto-Detect" then this rule will change the authentication to match the request identified.

- **URL:** http://127.0.0.1:8000/login (POST, `_token, email, password`)
- **Parameter:** `email`
- **Evidence:** `password`
- **Other Info:**
  - `userParam=email`
  - `userValue=admin@sppg.local`
  - `passwordParam=password`
  - `referer=http://127.0.0.1:8000/login`
  - `csrfToken=_token`
- **Instances:** 1

**Solution**
This is an informational alert rather than a vulnerability and so there is nothing to fix.

**Reference**
- https://www.zaproxy.org/docs/desktop/addons/authentication-helper/auth-req-id/

Plugin Id: 10111

---

### Informational — Modern Web Application

**Description**
The application appears to be a modern web application. If you need to explore it automatically then the Client Spider may well be more effective than the standard one.

- **URL:** http://127.0.0.1:8000/admin/absensi-manual (POST, `_method, _token, _token, divisi_id, hadir[], hadir[], hadir[], hadir[], jam_masuk, jam_pulang, manual_note, tanggal`)
- **Evidence:** an inline `<script>` containing a Laravel/Ignition error page rendered as markdown. Intisarinya:
  - Exception: `Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException - Method Not Allowed`
  - Message: The DELETE method is not supported for route `admin/absensi-manual`. Supported methods: GET, HEAD, POST.
  - Environment: PHP 8.2.12, Laravel 12.64.0, 127.0.0.1:8000
  - Content: full stack trace (frames 0–32, from `vendor\laravel\framework\...` to `public\index.php`), request `DELETE /admin/absensi-manual`, request headers (browser: Microsoft Edge 156 on Windows), cookies `XSRF-TOKEN` and `laravel-session` (`[REDACTED]`), "No routing data available", "No database queries detected".
- **Other Info:** No links have been found while there are scripts, which is an indication that this is a modern web application.
- **Instances:** 1

**Solution**
This is an informational alert and so no changes are required.

Plugin Id: 10109

> Temuan tambahan dari evidence ini: halaman error men-*debug* (stack trace, versi Laravel/PHP, header, dan cookie) tampil ke klien, yang menandakan `APP_DEBUG` kemungkinan masih `true`. Ini bukan alert ZAP resmi, tapi layak diperbaiki sebelum produksi.

---

### Informational — Re-examine Cache-control Directives

**Description**
The cache-control header has not been set properly or is missing, allowing the browser and proxies to cache content. For static assets like css, js, or image files this might be intended, however, the resources should be reviewed to ensure that no sensitive content will be cached.

- **URL:** https://edge-consumer-static.azureedge.net/mouse-gesture/config.json (GET)
- **Parameter:** `cache-control`
- **Evidence:** `public, max-age=432000`
- **Instances:** 1

**Solution**
For secure content, ensure the cache-control HTTP header is set with "no-cache, no-store, must-revalidate". If an asset should be cached consider setting the directives "public, max-age, immutable".

**Reference**
- https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html#web-content-caching
- https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control
- https://grayduck.mn/2021/09/13/cache-control-recommendations/

CWE Id: 525 · Plugin Id: 10015

---

### Informational — Retrieved from Cache

**Description**
The content was retrieved from a shared cache. If the response data is sensitive, personal or user-specific, this may result in sensitive information being leaked. In some cases, this may even result in a user gaining complete control of the session of another user, depending on the configuration of the caching components in use in their environment. This is primarily an issue where caching servers such as "proxy" caches are configured on the local network. This configuration is typically found in corporate or educational environments, for instance.

**Affected URLs (GET)** — Evidence: `HIT`
- https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css
- https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6
- https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
- https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js
- https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js

**Instances:** 5

**Solution**
Validate that the response does not contain sensitive, personal or user-specific information. If it does, consider the use of the following HTTP response headers, to limit, or prevent the content being stored and retrieved from the cache by another user:

```
Cache-Control: no-cache, no-store, must-revalidate, private
Pragma: no-cache
Expires: 0
```

This configuration directs both HTTP 1.0 and HTTP 1.1 compliant caching servers to not store the response, and to not retrieve the response (without validation) from the cache, in response to a similar request.

**Reference**
- https://datatracker.ietf.org/doc/html/rfc7234
- https://datatracker.ietf.org/doc/html/rfc7231
- https://www.rfc-editor.org/rfc/rfc9110.html

CWE Id: 525 · Plugin Id: 10050

---

### Informational — Session Management Response Identified

**Description**
The given response has been identified as containing a session management token. The 'Other Info' field contains a set of header tokens that can be used in the Header Based Session Management Method. If the request is in a context which has a Session Management Method set to "Auto-Detect" then this rule will change the session management to use the tokens identified.

**Affected URLs** — Parameter/Evidence: `laravel-session`; Other Info: `cookie:laravel-session`, `cookie:XSRF-TOKEN`
- GET http://127.0.0.1:8000/admin/absensi-manual
- GET http://127.0.0.1:8000/admin/dashboard
- GET http://127.0.0.1:8000/admin/divisions
- GET http://127.0.0.1:8000/admin/divisions/1/edit
- GET http://127.0.0.1:8000/admin/izin
- GET http://127.0.0.1:8000/admin/laporan
- GET http://127.0.0.1:8000/admin/laporan/export-pdf?bulan=2026-11
- GET http://127.0.0.1:8000/admin/payroll
- GET http://127.0.0.1:8000/admin/payroll/3
- GET http://127.0.0.1:8000/admin/relawan
- GET http://127.0.0.1:8000/login
- POST http://127.0.0.1:8000/admin/divisions/1
- POST http://127.0.0.1:8000/login

**Instances:** 13

**Solution**
This is an informational alert rather than a vulnerability and so there is nothing to fix.

**Reference**
- https://www.zaproxy.org/docs/desktop/addons/authentication-helper/session-mgmt-id/

Plugin Id: 10112

---

## Sequence Details

With the associated active scan results.
