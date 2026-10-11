# Upgrading

This development branch uses CakePHP 6 and PHP 8.5. Follow [Installation](../guide/installation.md) for development dependencies. The database type and number helper are tested against CakePHP 6; the decimal value-object dependency remains at its existing supported major version.

Older documentation claimed that the Number helper avoided floating-point formatting and that a configurable custom value-object class was supported. The helper delegates to CakePHP's number formatter, and the type directly constructs the standard Decimal class. Review code that depended on either claim.

If replacing a core decimal string mapping, update code that assumes entity values are strings. Cast explicitly when passing values to validators or APIs that require strings. Apply entity type annotations and review serializers, JSON responses and templates.


The unreleased locale-parser fix rejects malformed grouping instead of stripping every separator. For example, `19.90` returns `null` under `de_DE`; submit `19,90` for that locale. Grouping follows the locale, including secondary group sizes such as Indian numbering. Unparseable input and numeric prefixes with trailing invalid characters return `null` instead of throwing or accepting only the prefix. Localized string conversion no longer falls back to floating-point parsing. Scientific notation in request strings is rejected to prevent exponent expansion from exhausting memory; supply plain decimal strings in form and API payloads.
