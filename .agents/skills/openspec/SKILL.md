---
name: openspec
description: Manage specification-driven work with the OpenSpec CLI. Use when the user wants to explore an idea, propose and plan a change, revise planning artifacts, implement an approved change, sync delta specs, archive completed work, inspect OpenSpec status, or decide the next OpenSpec action in a repository.
---

# OpenSpec

Route a request to the OpenSpec workflow skills stored in this repository under `.agents/skills`. Keep this router small; the workflow-specific skills are the source of truth.

## Route the request

Interpret the first word after `$openspec` as an action. Accept natural-language equivalents in Spanish or English.

- `explore` or `explorar`: read `../openspec-explore/SKILL.md` completely and follow it.
- `propose`, `proponer`, or `planificar`: read `../openspec-propose/SKILL.md` completely and follow it.
- `apply`, `aplicar`, `implementar`, or `continuar implementacion`: read `../openspec-apply-change/SKILL.md` completely and follow it.
- `update`, `actualizar`, or `revisar plan`: read `../openspec-update-change/SKILL.md` completely and follow it.
- `sync` or `sincronizar`: read `../openspec-sync-specs/SKILL.md` completely and follow it.
- `archive`, `archivar`, or `finalizar`: read `../openspec-archive-change/SKILL.md` completely and follow it.

If the user states an intent instead of an action, select the single workflow that clearly matches it. If two workflows could materially differ, ask one concise clarification before proceeding.

## Invocation without an action

Run `openspec list --json` and, when a relevant active change exists, run `openspec status --change "<name>" --json`. Recommend the most useful next action without executing a mutating workflow. Present the callable forms:

- `$openspec explore <tema>`
- `$openspec propose <cambio>`
- `$openspec apply <cambio>`
- `$openspec update <cambio>`
- `$openspec sync <cambio>`
- `$openspec archive <cambio>`

## Execution rules

- Keep the working directory at the user's project root.
- Verify that `openspec` is available before the first CLI operation. If unavailable, report the missing CLI and stop the OpenSpec workflow.
- Preserve any `--store <id>` argument required by the canonical workflow.
- Use the current app's available user-input mechanism when a generated workflow mentions a tool name that is unavailable.
- If `openspec update` regenerates workflows under `.codex/skills`, promote the updated workflow folders back into `.agents/skills` before relying on this router.
