#!/usr/bin/env bash
# Boot the local PHP dev server with our session save path.
# Open http://127.0.0.1:8765
set -euo pipefail
cd "$(dirname "$0")/.."

PORT="${1:-8765}"
SESSDIR="$(pwd)/storage/sessions"
mkdir -p "$SESSDIR"
chmod 700 "$SESSDIR"

echo "AviatorTutor dev server → http://127.0.0.1:${PORT}"
echo "  session.save_path = $SESSDIR"
echo "  doc root          = $(pwd)/public"
exec php -S "127.0.0.1:${PORT}" \
    -t public \
    -d "session.save_path=$SESSDIR" \
    -d display_errors=1 \
    public/router.php
