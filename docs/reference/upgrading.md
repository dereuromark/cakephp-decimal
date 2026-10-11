# Upgrading

Review the installed version's Composer constraints before upgrading CakePHP or PHP. The current code targets CakePHP 5; CakePHP 6 compatibility requires testing against that release.

Older documentation claimed that the Number helper avoided floating-point formatting and that a configurable custom value-object class was supported. The helper delegates to CakePHP's number formatter, and the type directly constructs the standard Decimal class. Review code that depended on either claim.

If replacing a core decimal string mapping, update code that assumes entity values are strings. Cast explicitly when passing values to validators or APIs that require strings. Apply entity type annotations and review serializers, JSON responses and templates.

The current cleanup removes unused mock expectations notices and updates conversion comments. It does not change the database schema or the default type conversion options.
