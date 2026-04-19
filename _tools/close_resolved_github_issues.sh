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

RESOLVED_ISSUES_JSON='[
  {
    "number": 1088,
    "title": "Admin page login loop",
    "message": "Closing as resolved by hardening localhost/session domain handling in the installer/runtime constants and host normalization for localhost with port. This addresses admin login redirect loops caused by dropped session cookies. Please reopen if you can still reproduce on the latest code."
  },
  {
    "number": 1062,
    "title": "Blank index after removing _install",
    "message": "Closing as resolved. The bootstrap path handling now has stronger protected-path detection plus a clear configuration error path when PH7_PATH_PROTECTED is invalid, which fixes blank page behavior after removing _install. Please reopen with fresh logs if it still occurs."
  },
  {
    "number": 1054,
    "title": "Blank page after deleting/renaming _install",
    "message": "Closing as resolved by the same protected-path and bootstrap guard improvements that address blank page behavior after _install removal. If your host still shows a blank page, please reopen with PHP error logs and your _constants.php path values."
  },
  {
    "number": 324,
    "title": "Fake profile emails with accented chars",
    "message": "Closing as resolved. Fake profile generation now uses safe RFC-compatible emails for generated members/affiliates/subscribers, preventing accented mailbox values that break newsletter delivery validation."
  },
  {
    "number": 669,
    "title": "PayPal verification fails",
    "message": "Closing as resolved by modernizing the PayPal IPN verification endpoint to ipnpb.* and using a host header derived from the active endpoint. This fixes legacy verification failures returning permission/access errors in live mode."
  }
]'

echo "Authenticated as: ${AUTH_LOGIN}"
echo "Repository: ${REPO}"
echo "Dry run: ${DRY_RUN}"
echo

echo "Resolved issue queue:"
jq -r '.[] | "- #\(.number) \(.title)"' <<<"$RESOLVED_ISSUES_JSON"
echo
read -r -p "Proceed with closing these issues? [y/N]: " PROCEED
if [[ "${PROCEED,,}" != "y" ]]; then
  echo "Aborted."
  exit 0
fi

CLOSED_COUNT=0
SKIPPED_COUNT=0
FAILED_COUNT=0

while IFS= read -r row; do
  ISSUE_NUMBER="$(jq -r '.number' <<<"$row")"
  ISSUE_TITLE="$(jq -r '.title' <<<"$row")"
  ISSUE_MESSAGE="$(jq -r '.message' <<<"$row")"

  echo "Processing #${ISSUE_NUMBER} (${ISSUE_TITLE})..."

  ISSUE_JSON="$(api GET "/repos/${REPO}/issues/${ISSUE_NUMBER}" || true)"
  if [[ -z "$ISSUE_JSON" ]]; then
    echo "  - Failed to fetch issue metadata."
    ((FAILED_COUNT+=1))
    continue
  fi

  ISSUE_STATE="$(jq -r '.state // empty' <<<"$ISSUE_JSON")"
  if [[ "$ISSUE_STATE" != "open" ]]; then
    echo "  - Skipped (state=${ISSUE_STATE:-unknown})."
    ((SKIPPED_COUNT+=1))
    continue
  fi

  if [[ "$DRY_RUN" -eq 1 ]]; then
    echo "  - DRY RUN: would comment and close."
    ((SKIPPED_COUNT+=1))
    continue
  fi

  COMMENT_PAYLOAD="$(jq -nc --arg body "$ISSUE_MESSAGE" '{body: $body}')"
  if ! api POST "/repos/${REPO}/issues/${ISSUE_NUMBER}/comments" "$COMMENT_PAYLOAD" >/dev/null; then
    echo "  - Failed to post closure comment."
    ((FAILED_COUNT+=1))
    continue
  fi

  CLOSE_PAYLOAD='{"state":"closed"}'
  if ! api PATCH "/repos/${REPO}/issues/${ISSUE_NUMBER}" "$CLOSE_PAYLOAD" >/dev/null; then
    echo "  - Failed to close issue."
    ((FAILED_COUNT+=1))
    continue
  fi

  echo "  - Closed."
  ((CLOSED_COUNT+=1))
done < <(jq -c '.[]' <<<"$RESOLVED_ISSUES_JSON")

echo
echo "Done."
echo "Closed:  ${CLOSED_COUNT}"
echo "Skipped: ${SKIPPED_COUNT}"
echo "Failed:  ${FAILED_COUNT}"
