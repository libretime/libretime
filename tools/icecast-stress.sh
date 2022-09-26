#!/usr/bin/env bash

set -eu

error() {
  echo >&2 "error: $*"
  exit 1
}

command -v curl > /dev/null || error "curl command not found!"

# Run concurrent curls which download from url to /dev/null.
url="$1"

# max concurrent calls
max=1000
# call duration (in seconds)
duration=100
# number of calls to start in batch
batch=10
# time to wait before starting a new batch of calls (in seconds)
delay=1

count=0
while [[ "$count" -le "$max" ]]; do
  echo "starting $batch new calls ($count)"
  for ((i = 1; i <= batch; i++)); do
    curl -o /dev/null -m "$duration" -s "$url" &
  done
  count=$((count + batch))

  sleep "$delay"
done
echo "waiting for calls to finish"
wait
echo "done"
