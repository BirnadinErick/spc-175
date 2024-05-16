#!/bin/bash

# Read connection data from XML file
read_xml() {
    local xml_file="$1"
    server=$(xmllint --xpath 'string(//Host)' "$xml_file")
    username=$(xmllint --xpath 'string(//User)' "$xml_file")
    password=$(xmllint --xpath 'string(//Pass)' "$xml_file")
}

# set the DEBUG flag to false in Fend and Bend
echo "setting DEBUG flag false..."
sed -i 's/const DEBUG = true;/const DEBUG = false;/' "./src/config/global.ts"
sed -i 's/define("DEBUG", true);/define("DEBUG", false);/' "./api/v1/index.php"

# Execute pnpm build
echo "Building frontend..."
pnpm build > fend-build.log

# clean previous build
echo "Cleaning upload stage..."
rm -rf app/
mkdir app

# move files
echo "Moving fend..."
mv dist/* app/
echo "Copying bend..."
cp -r api/ app/
echo "Copying route middleware..."
cp .htaccess app/

# Read connection data from XML file
xml_file="upstream-config.xml"
echo "Reading server manifest..."
read_xml "$xml_file"

# Transfer files via sftp
echo "Transferring files to server..."
sftp $username@$server <<EOF
put -r app/*
exit
EOF

rm fend-build.log

# set the DEBUG flag back to true 
echo "setting DEBUG flag true..."
sed -i 's/const DEBUG = false;/const DEBUG = true;/' "./src/config/global.ts"
sed -i 's/define("DEBUG", false);/define("DEBUG", true);/' "./api/v1/index.php"

echo "Project sync."

