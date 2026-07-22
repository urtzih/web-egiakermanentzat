---
name: egia-privacy-compliance
description: Audit and maintain privacy, cookies, legal pages, consent architecture, security headers, and bilingual Spanish/Basque disclosures for the Egia Kermanentzat WordPress repository. Use when adding or changing services, browser storage, third-party resources, forms, donations, hosting, analytics, legal identity, or public privacy content.
---

# Egia Privacy Compliance

Keep the public site data-minimal and make legal claims match observable behavior. Treat the published copy as an informed technical draft until a qualified legal and Basque-language review is recorded.

## Workflow

1. Read `references/review-checklist.md` and the repository privacy documents.
2. Inventory every request, cookie, browser-storage access, form, embed, external resource, account, email flow, transfer flow, log, and processor affected by the change.
3. Compare the inventory with `kermanentzat_service_registry()` and its explicit version.
4. Classify storage as strictly necessary or optional. Do not add a banner when the optional registry is empty.
5. For an optional service, define purpose, provider, data, trigger, legal basis, category, withdrawal path, retention criterion, transfer safeguards, and adapter before loading it.
6. Update both Spanish and Basque pages from the central seed. Never invent registration numbers, retention periods, processor terms, tax benefits, or representatives.
7. Update `docs/COOKIE_INVENTORY.md`, `docs/PRIVACY_AUDIT.md`, the registry version, and the test evidence in the same change.
8. Run PHP syntax checks and `scripts/test-privacy.ps1`; then manually verify both languages, mobile/desktop, keyboard, focus, headings, zoom, contrast, and reduced motion.

## Consent decision

- If no optional service writes or reads browser data and no optional third-party resource loads automatically, keep consent UI and preference storage absent.
- If a future optional service is proposed, keep it disabled until its adapter is registered and affirmative category consent can be demonstrated.
- Never load Analytics, Tag Manager, pixels, embeds, remote fonts, or a consent manager merely to prepare for possible future use.
- Necessary WordPress administration cookies belong to the restricted administration scope, not the anonymous public frontend.

## Legal-content rules

- Publish only verified association identity fields from `kermanentzat_legal_config()`.
- Keep the registration number, hosting, Gmail contractual arrangement, log retention, operational contacts, and donation tax status pending until evidence exists.
- Describe only real processing and select the applicable legal basis; do not default every activity to consent.
- Express conservation as validated legal or operational criteria when an exact period is not established.
- Explain bank-transfer recipient, general purpose, incident handling, and the absence of any tax-deduction promise.
- Keep external providers as click-initiated links unless the service registry and inventory say otherwise.

## Safety boundaries

- Do not store or publish identity documents, signatures, personal representative data, credentials, bank certificates, or unverified identifiers.
- Do not install dependencies, plugins, executable scripts, or external integrations as part of this skill.
- Do not claim full GDPR, LSSI, tax, or accessibility compliance. Record limitations and human validations still required.

