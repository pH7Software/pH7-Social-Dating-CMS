#!/usr/bin/env bash
set -euo pipefail

REPO="pH7Software/pH7-Social-Dating-CMS"
DRY_RUN=0

usage() {
  cat <<'EOF'
Usage: _tools/close_resolved_github_issues.sh [--repo owner/name] [--dry-run]

Options:
  --repo      Override target repository (default: pH7Software/pH7-Social-Dating-CMS)
  --dry-run   Print planned actions without posting comments/closing issues
  -h, --help  Show this help

Authentication:
  - If `gh` is installed and authenticated, this script uses your `gh auth token`.
  - Otherwise, it prompts for a GitHub personal access token (fine-grained token with
    Issues: Read/Write permission, or classic `repo` scope).

Behavior:
  - Fetches all open issues in the repository (non-PRs).
  - Applies triage policy:
    - Mark as completed when a concrete fix was shipped in code.
    - Mark as not planned for stale support requests/feature backlog items.
  - Posts a closure note and closes each issue.
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --repo)
      REPO="${2:-}"
      shift 2
      ;;
    --dry-run)
      DRY_RUN=1
      shift
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Unknown argument: $1" >&2
      usage
      exit 1
      ;;
  esac
done

require_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Missing required command: $1" >&2
    exit 1
  fi
}

require_cmd curl
require_cmd jq

get_token_from_gh() {
  if command -v gh >/dev/null 2>&1; then
    if gh auth status >/dev/null 2>&1; then
      gh auth token 2>/dev/null || true
      return 0
    fi
  fi
  return 1
}

API_BASE="https://api.github.com"
TOKEN="${GITHUB_TOKEN:-${GH_TOKEN:-}}"

if [[ -z "$TOKEN" ]]; then
  TOKEN="$(get_token_from_gh || true)"
fi

if [[ -z "$TOKEN" ]]; then
  echo "GitHub authentication is required."
  echo "Paste a GitHub token with permission to read/write issues for ${REPO}."
  read -r -s -p "GitHub token: " TOKEN
  echo
fi

api() {
  local method="$1"
  local endpoint="$2"
  local data="${3:-}"

  if [[ -n "$data" ]]; then
    curl -fsS \
      -X "$method" \
      -H "Authorization: Bearer ${TOKEN}" \
      -H "Accept: application/vnd.github+json" \
      -H "X-GitHub-Api-Version: 2022-11-28" \
      "${API_BASE}${endpoint}" \
      -d "$data"
  else
    curl -fsS \
      -X "$method" \
      -H "Authorization: Bearer ${TOKEN}" \
      -H "Accept: application/vnd.github+json" \
      -H "X-GitHub-Api-Version: 2022-11-28" \
      "${API_BASE}${endpoint}"
  fi
}

AUTH_LOGIN="$(api GET "/user" | jq -r '.login // empty')"
if [[ -z "$AUTH_LOGIN" ]]; then
  echo "Authentication failed. Please verify your token permissions." >&2
  exit 1
fi

is_completed_issue() {
  case "$1" in
    324|554|669|1024|1033|1054|1062|1088|1136) return 0 ;;
    *) return 1 ;;
  esac
}

get_completed_message() {
  case "$1" in
    324)
      cat <<'EOF'
Thanks for reporting this.

This is now fixed in 18.x. Fake profile generation now uses safe RFC-compatible email addresses, so accented mailbox values from faker data no longer break mail/newsletter validation.
EOF
      ;;
    554)
      cat <<'EOF'
Thanks for reporting this.

This is now fixed in 18.x. Docker Compose mount/working-dir syntax has been corrected, including the invalid volume mode pattern and DB port mapping, so containers can start correctly.
EOF
      ;;
    669)
      cat <<'EOF'
Thanks for the detailed report.

This is now fixed in 18.x. PayPal IPN verification has been updated to modern `ipnpb.*` endpoints with a host header derived from the active endpoint, resolving verification failures that returned access/permission errors.
EOF
      ;;
    1024)
      cat <<'EOF'
Thanks for reporting this.

This has been addressed in 18.x through static/gzip fallback hardening. When cache/static retrieval fails, the system now safely falls back to local static files instead of failing hard, which resolves this class of asset loading failures.
EOF
      ;;
    1033)
      cat <<'EOF'
Thanks for reporting this.

This has been addressed in 18.x through multiple runtime hardening fixes (installer/protected path handling and static asset fallback), which resolve the common 500 scenarios seen right after installation.
EOF
      ;;
    1054|1062)
      cat <<'EOF'
Thanks for reporting this.

This is now fixed in 18.x. Protected path/bootstrap handling has been hardened, including clear error reporting for invalid `_protected` path configuration, resolving blank-page behavior after `_install` removal in misconfigured environments.
EOF
      ;;
    1088)
      cat <<'EOF'
Thanks for reporting this.

This is now fixed in 18.x. Admin login loop behavior was tied to host/cookie/session edge cases (localhost+port and invalid cookie-domain values). Host normalization and cookie-domain safety fixes resolve this flow.
EOF
      ;;
    1136)
      cat <<'EOF'
Thanks for reporting this.

This has been addressed in 18.x by hardening HTTPS/proxy protocol detection (`HTTPS=on|1`, forwarded proto/SSL), which fixes mixed-protocol URL generation issues that caused missing front-page icons/assets after SSL enablement.
EOF
      ;;
    *)
      cat <<'EOF'
Thanks for the report.

This has been addressed in current 18.x code. Please pull latest and reopen with exact repro + logs if it still occurs.
EOF
      ;;
  esac
}

is_not_planned_label_set() {
  local labels="$1"
  if [[ "$labels" == *"enhancement"* ]] || [[ "$labels" == *"feature"* ]] || [[ "$labels" == *"suggestion"* ]] || [[ "$labels" == *"improvement"* ]] || [[ "$labels" == *"question"* ]]; then
    return 0
  fi
  return 1
}

get_not_planned_message() {
  local issue_number="$1"
  local labels="$2"

  if [[ "$issue_number" == "765" ]]; then
    cat <<'EOF'
Thanks for the report.

This issue is covered by the UTF query-string encoding track already handled in #737. Closing this duplicate thread for tracker hygiene. If you still see a reproducible case on latest 18.x, please open a new ticket with exact steps and sample payload.
EOF
    return
  fi

  if is_not_planned_label_set "$labels"; then
    cat <<'EOF'
Thanks for the suggestion.

Reviewed during tracker cleanup. This is currently outside the active core roadmap, so I’m closing as not planned for now. If priorities change, we can reopen/revisit with a concrete implementation proposal.
EOF
    return
  fi

  cat <<'EOF'
Thanks for the report.

Reviewed during tracker cleanup. This thread is stale and not actionable as-is (missing reproducible current-version details), so I’m closing it for hygiene. Please open a fresh issue against latest 18.x with exact repro steps and logs if this still happens.
EOF
}

echo "Authenticated as: ${AUTH_LOGIN}"
echo "Repository: ${REPO}"
echo "Dry run: ${DRY_RUN}"
echo

OPEN_ISSUES_JSON="$(api GET "/repos/${REPO}/issues?state=open&per_page=100")"
OPEN_ISSUES_COUNT="$(jq '[.[] | select(.pull_request|not)] | length' <<<"$OPEN_ISSUES_JSON")"

if [[ "$OPEN_ISSUES_COUNT" -eq 0 ]]; then
  echo "No open issues to process."
  exit 0
fi

echo "Open issues queued: ${OPEN_ISSUES_COUNT}"
echo
jq -r '.[] | select(.pull_request|not) | "- #\(.number) \(.title)"' <<<"$OPEN_ISSUES_JSON"
echo
read -r -p "Proceed with triage+closure on ALL open issues above? [y/N]: " PROCEED
PROCEED_LOWER="$(printf '%s' "${PROCEED}" | tr '[:upper:]' '[:lower:]')"
if [[ "${PROCEED_LOWER}" != "y" ]]; then
  echo "Aborted."
  exit 0
fi

read -r -p "Type CLOSE_ALL to confirm: " HARD_CONFIRM
if [[ "$HARD_CONFIRM" != "CLOSE_ALL" ]]; then
  echo "Aborted."
  exit 0
fi

CLOSED_COUNT=0
SKIPPED_COUNT=0
FAILED_COUNT=0
COMPLETED_COUNT=0
NOT_PLANNED_COUNT=0

while IFS= read -r issue; do
  ISSUE_NUMBER="$(jq -r '.number' <<<"$issue")"
  ISSUE_TITLE="$(jq -r '.title' <<<"$issue")"
  ISSUE_LABELS="$(jq -r '(.labels // [] | map(.name) | join(","))' <<<"$issue")"

  echo "Processing #${ISSUE_NUMBER} (${ISSUE_TITLE})..."

  if is_completed_issue "$ISSUE_NUMBER"; then
    ISSUE_STATE_REASON="completed"
    ISSUE_MESSAGE="$(get_completed_message "$ISSUE_NUMBER")"
    ((COMPLETED_COUNT+=1))
  else
    ISSUE_STATE_REASON="not_planned"
    ISSUE_MESSAGE="$(get_not_planned_message "$ISSUE_NUMBER" "$ISSUE_LABELS")"
    ((NOT_PLANNED_COUNT+=1))
  fi

  if [[ "$DRY_RUN" -eq 1 ]]; then
    echo "  - DRY RUN: would comment and close with state_reason=${ISSUE_STATE_REASON}."
    ((SKIPPED_COUNT+=1))
    continue
  fi

  COMMENT_PAYLOAD="$(jq -nc --arg body "$ISSUE_MESSAGE" '{body: $body}')"
  if ! api POST "/repos/${REPO}/issues/${ISSUE_NUMBER}/comments" "$COMMENT_PAYLOAD" >/dev/null; then
    echo "  - Failed to post closure comment."
    ((FAILED_COUNT+=1))
    continue
  fi

  CLOSE_PAYLOAD="$(jq -nc --arg reason "$ISSUE_STATE_REASON" '{"state":"closed","state_reason":$reason}')"
  if ! api PATCH "/repos/${REPO}/issues/${ISSUE_NUMBER}" "$CLOSE_PAYLOAD" >/dev/null; then
    echo "  - Failed to close issue."
    ((FAILED_COUNT+=1))
    continue
  fi

  echo "  - Closed."
  ((CLOSED_COUNT+=1))
done < <(jq -c '.[] | select(.pull_request|not)' <<<"$OPEN_ISSUES_JSON")

echo
echo "Done."
echo "Closed:  ${CLOSED_COUNT}"
echo "Skipped: ${SKIPPED_COUNT}"
echo "Failed:  ${FAILED_COUNT}"
echo "Completed state: ${COMPLETED_COUNT}"
echo "Not planned state: ${NOT_PLANNED_COUNT}"
