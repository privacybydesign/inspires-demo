#!/usr/bin/env bash
# Local "deploy": poll origin/main; on a new commit, rebuild and restart.
# Run on the laptop next to the browser with the slides. No infra deploy rights
# needed — a merge to main is the whole signal.
set -euo pipefail
cd "$(dirname "$0")/.."

BRANCH="${BRANCH:-main}"
INTERVAL="${INTERVAL:-10}"

echo "watching origin/$BRANCH every ${INTERVAL}s — Ctrl-C to stop"
last=""
while true; do
  git fetch -q origin "$BRANCH" || { sleep "$INTERVAL"; continue; }
  head="$(git rev-parse "origin/$BRANCH")"
  if [ "$head" != "$last" ]; then
    echo "$(date +%H:%M:%S)  new commit $head — deploying"
    git checkout -q "$BRANCH"
    git reset -q --hard "origin/$BRANCH"
    docker compose up -d --build
    echo "$(date +%H:%M:%S)  deployed. /health:"
    curl -s http://localhost:8080/health.php | sed 's/^/    /' || true
    last="$head"
  fi
  sleep "$INTERVAL"
done
