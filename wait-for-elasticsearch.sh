#!/bin/bash

echo "Waiting for Elasticsearch to start..."
while ! curl -s http://elasticsearch:9200/ >/dev/null; do
    sleep 1
done

echo "Elasticsearch is up and running!"
exec "$@"
