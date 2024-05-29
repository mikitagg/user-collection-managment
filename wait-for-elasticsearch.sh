#!/bin/sh

until curl -s http://localhost:9200 -o /dev/null -w '%{http_code}' | grep -q 200; do
  echo "Waiting for Elasticsearch..."
  sleep 1
done