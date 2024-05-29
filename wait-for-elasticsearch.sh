#!/bin/bash

set -e

HOST="$1"
PORT="$2"
TIMEOUT="${3:-30}"

while true; do
  if nc -z "$HOST" "$PORT"; then
    echo "Elasticsearch is up on $HOST:$PORT"
    break
  else
    echo "Elasticsearch is not yet up, retrying in 1 second..."
    sleep 1
  fi

  if [[ $((TIMEOUT--)) -eq 0 ]]; then
    echo "Timeout waiting for Elasticsearch on $HOST:$PORT"
    exit 1
  fi
done

# Run your application after Elasticsearch is ready
exec "$@"