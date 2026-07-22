# Privacy review checklist

## Authoritative references

- LSSI articles 10 and 22.2: https://www.boe.es/buscar/act.php?id=BOE-A-2002-13758#a10
- GDPR article 13: https://eur-lex.europa.eu/eli/reg/2016/679/oj?locale=es
- AEPD cookie guide: https://www.aepd.es/es/documento/guia-cookies.pdf
- AEPD layered information guidance: https://www.aepd.es/preguntas-frecuentes/2-tus-obligaciones-como-responsable-del-tratamiento/6-el-deber-de-informacion/FAQ-0217-que-informacion-debe-facilitarse-cuando-los-datos-se-obtengan-directamente-del-afectado
- Spanish Law 49/2002: https://boe.es/buscar/act.php?id=BOE-A-2002-25039
- Google Consent Mode, only if Analytics is approved later: https://developers.google.com/tag-platform/security/guides/consent

## Required change evidence

- Anonymous responses have no `Set-Cookie`.
- Automatic resources are same-origin.
- No unregistered storage API, beacon, iframe, pixel, or tracking request exists.
- Six legal routes return 200 and expose reciprocal `hreflang` plus a contextual language switch.
- Footer legal links exist in both languages.
- Security headers match the documented policy.
- Seed output matches source-controlled content.
- Inventory, registry version, tests, and pending human validations are current.

## Stop conditions

Stop publication and mark the item pending when documentary identity, processor terms, international transfer safeguards, retention, tax treatment, or a legally reviewed Basque translation cannot be verified.

