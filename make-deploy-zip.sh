#!/bin/bash

set -euo pipefail

APP_NAME="${1:-deploy}"
TIMESTAMP="$(date +"%Y-%m-%d_%H-%M-%S")"
OUT_DIR="./deploy"
ZIP_NAME="${APP_NAME}_${TIMESTAMP}.zip"
ZIP_PATH="${OUT_DIR}/${ZIP_NAME}"

mkdir -p "$OUT_DIR"

echo "Creating deploy package: $ZIP_PATH"

zip -r "$ZIP_PATH" . \
  -x "*.DS_Store" \
  -x ".git/*" \
  -x ".gitignore" \
  -x "node_modules/*" \
  -x "storage/logs/*" \
  -x "storage/framework/cache/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/testing/*" \
  -x "storage/framework/views/*" \
  -x "storage/app/private/*" \
  -x "bootstrap/cache/*.php" \
  -x ".env" \
  -x ".env.*" \
  -x "deploy/*" \
  -x "*.zip"

echo "Done."
echo "Package created at: $ZIP_PATH"

