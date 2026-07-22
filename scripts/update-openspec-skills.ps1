[CmdletBinding()]
param(
    [string]$ProjectRoot
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($ProjectRoot)) {
    $ProjectRoot = Split-Path -Parent $PSScriptRoot
}

$resolvedRoot = (Resolve-Path -LiteralPath $ProjectRoot).Path
$scriptRoot = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))

if (-not [string]::Equals($resolvedRoot, $scriptRoot, [StringComparison]::OrdinalIgnoreCase)) {
    throw "ProjectRoot must resolve to the repository that contains this script: $scriptRoot"
}

$openSpecConfig = Join-Path $resolvedRoot 'openspec\config.yaml'
$agentsSkills = Join-Path $resolvedRoot '.agents\skills'
$codexRoot = Join-Path $resolvedRoot '.codex'
$codexSkills = Join-Path $codexRoot 'skills'
$workflowNames = @(
    'openspec-apply-change',
    'openspec-archive-change',
    'openspec-explore',
    'openspec-propose',
    'openspec-sync-specs',
    'openspec-update-change'
)

if (-not (Test-Path -LiteralPath $openSpecConfig -PathType Leaf)) {
    throw "OpenSpec config not found: $openSpecConfig"
}
if (-not (Test-Path -LiteralPath $agentsSkills -PathType Container)) {
    throw "Canonical skills directory not found: $agentsSkills"
}

$openSpecCommand = Get-Command openspec -ErrorAction Stop
$configBefore = (& $openSpecCommand.Source config list --json | Out-String | ConvertFrom-Json)

# OpenSpec 1.6 detects Codex by finding at least one generated skill under
# .codex/skills. Bootstrap that temporary location from the canonical copy when
# a previous successful synchronization has removed it.
$bootstrapName = 'openspec-propose'
$bootstrapSource = Join-Path $agentsSkills "$bootstrapName\SKILL.md"
$bootstrapTargetDir = Join-Path $codexSkills $bootstrapName
$bootstrapTarget = Join-Path $bootstrapTargetDir 'SKILL.md'
if (-not (Test-Path -LiteralPath $bootstrapSource -PathType Leaf)) {
    throw "Bootstrap workflow not found: $bootstrapSource"
}
if (-not (Test-Path -LiteralPath $bootstrapTarget -PathType Leaf)) {
    New-Item -ItemType Directory -Path $bootstrapTargetDir -Force | Out-Null
    Copy-Item -LiteralPath $bootstrapSource -Destination $bootstrapTarget -Force
}

$updateExitCode = 1
try {
    & $openSpecCommand.Source update --force $resolvedRoot
    $updateExitCode = $LASTEXITCODE
}
finally {
    # OpenSpec may migrate its machine-global profile while updating a legacy
    # project. This repository-local helper must not leave that side effect.
    & $openSpecCommand.Source config set profile $configBefore.profile | Out-Null
    & $openSpecCommand.Source config set delivery $configBefore.delivery | Out-Null
    if ($configBefore.PSObject.Properties.Name -contains 'workflows') {
        $workflowsJson = ConvertTo-Json -Compress -InputObject @($configBefore.workflows)
        & $openSpecCommand.Source config set workflows $workflowsJson | Out-Null
    }
    else {
        & $openSpecCommand.Source config unset workflows | Out-Null
    }
}
if ($updateExitCode -ne 0) {
    throw "openspec update failed with exit code $updateExitCode. Temporary files were kept for inspection."
}

foreach ($name in $workflowNames) {
    $generatedFile = Join-Path $codexSkills "$name\SKILL.md"
    if (-not (Test-Path -LiteralPath $generatedFile -PathType Leaf)) {
        throw "Expected generated workflow not found: $generatedFile"
    }

    $content = Get-Content -Raw -LiteralPath $generatedFile
    if ($content -notmatch "(?m)^name:\s+$([regex]::Escape($name))\s*$") {
        throw "Generated workflow has an unexpected frontmatter name: $generatedFile"
    }
}

foreach ($name in $workflowNames) {
    $generatedDir = Join-Path $codexSkills $name
    $canonicalDir = Join-Path $agentsSkills $name

    if (Test-Path -LiteralPath $canonicalDir) {
        $canonicalPath = [IO.Path]::GetFullPath($canonicalDir)
        $expectedPrefix = [IO.Path]::GetFullPath($agentsSkills) + [IO.Path]::DirectorySeparatorChar
        if (-not $canonicalPath.StartsWith($expectedPrefix, [StringComparison]::OrdinalIgnoreCase)) {
            throw "Refusing to replace a path outside .agents/skills: $canonicalPath"
        }
        Remove-Item -LiteralPath $canonicalPath -Recurse -Force
    }
    Copy-Item -LiteralPath $generatedDir -Destination $canonicalDir -Recurse -Force

    # OpenSpec 1.6 emits `compatibility` as a top-level frontmatter property,
    # while Codex's skill validator accepts it under `metadata`. Preserve the
    # information in the compatible location.
    $canonicalFile = Join-Path $canonicalDir 'SKILL.md'
    $canonicalContent = [IO.File]::ReadAllText($canonicalFile, [Text.Encoding]::UTF8)
    $compatibilityPattern = '(?m)^compatibility:\s*(.+)\r?\nmetadata:\r?\n'
    if ($canonicalContent -match $compatibilityPattern) {
        $replacement = "metadata:`n  compatibility: `$1`n"
        $canonicalContent = [regex]::Replace($canonicalContent, $compatibilityPattern, $replacement, 1)
        [IO.File]::WriteAllText($canonicalFile, $canonicalContent, [Text.UTF8Encoding]::new($false))
    }
}

$codexSkillsPath = [IO.Path]::GetFullPath($codexSkills)
$expectedCodexSkills = [IO.Path]::GetFullPath((Join-Path $resolvedRoot '.codex\skills'))
if (-not [string]::Equals($codexSkillsPath, $expectedCodexSkills, [StringComparison]::OrdinalIgnoreCase)) {
    throw "Refusing to remove an unexpected Codex skills path: $codexSkillsPath"
}
Remove-Item -LiteralPath $codexSkillsPath -Recurse -Force

if ((Test-Path -LiteralPath $codexRoot -PathType Container) -and
    -not (Get-ChildItem -LiteralPath $codexRoot -Force | Select-Object -First 1)) {
    Remove-Item -LiteralPath $codexRoot -Force
}

Write-Host "OpenSpec workflows updated in $agentsSkills"
Write-Host 'Temporary .codex/skills copies removed.'
