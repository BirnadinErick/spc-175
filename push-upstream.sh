#!/bin/bash

# Read connection data from XML file
read_xml() {
    local xml_file="$1"
    server=$(xmllint --xpath 'string(//Host)' "$xml_file")
    username=$(xmllint --xpath 'string(//User)' "$xml_file")
    password=$(xmllint --xpath 'string(//Pass)' "$xml_file")
}

# Execute pnpm build
echo "Building project..."
pnpm build

# Read connection data from XML file
xml_file="upstream-config.xml"
read_xml "$xml_file"

# Transfer files via sftp
echo "Transferring files to server..."
sftp $username@$server <<EOF
put -r dist/*
exit
EOF

echo "Files transferred successfully."

