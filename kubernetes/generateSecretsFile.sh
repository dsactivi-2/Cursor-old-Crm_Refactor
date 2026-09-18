#!/usr/bin/env bash

# Generate a secrets file for the cluster
echo "apiVersion: v1"
echo "kind: Secret"
echo "metadata:"
echo "  name: env-secrets"
echo "type: Opaque"
echo "data:"

echo "  DB_HOST: $(echo -n $K8S_DB_HOST | base64 -w 0)"
echo "  DB_DATABASE: $(echo -n $K8S_DB_DATABASE | base64 -w 0)"
echo "  DB_USER: $(echo -n $K8S_DB_USER | base64 -w 0)"
echo "  DB_PASSWORD: $(echo -n $K8S_DB_PASSWORD | base64 -w 0)"
