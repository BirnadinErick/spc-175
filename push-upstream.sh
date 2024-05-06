#!/bin/bash

# Read connection data from XML file
read_xml() {
    local xml_file="$1"
    server=$(xmllint --xpath 'string(//Host)' "$xml_file")
    username=$(xmllint --xpath 'string(//User)' "$xml_file")
    password=$(xmllint --xpath 'string(//Pass)' "$xml_file")
}

# Execute pnpm build
echo "Building frontend..."
pnpm build

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

echo "Project sync."

