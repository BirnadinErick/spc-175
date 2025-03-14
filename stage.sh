#!/bin/bash

set -e

echo "setting DEBUG flag false..."
sed -i 's/const DEBUG = true;/const DEBUG = false;/' "./src/config/global.ts"
sed -i 's/define("DEBUG", true);/define("DEBUG", false);/' "./api/v1/index.php"

echo "Building frontend..."
pnpm build

# clean previous build
echo "Cleaning upload stage..."
rm -rf staging/*

# move files
echo "Moving fend..."
mv dist/* staging/
echo "Copying bend..."
cp -r api/ staging/

# proper env file
echo "moving .env file..."
rm staging/api/v2/.env
cp .prod staging/api/v2/
mv staging/api/v2/.prod staging/api/v2/.env

# set the DEBUG flag back to true
echo "setting DEBUG flag true..."
sed -i 's/const DEBUG = false;/const DEBUG = true;/' "./src/config/global.ts"
sed -i 's/define("DEBUG", false);/define("DEBUG", true);/' "./api/v1/index.php"

echo "Project staged."

