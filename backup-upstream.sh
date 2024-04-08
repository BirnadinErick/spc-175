#!/bin/bash

# Read connection data from XML file
read_xml() {
    local xml_file="$1"
    server=$(xmllint --xpath 'string(//Host)' "$xml_file")
    username=$(xmllint --xpath 'string(//User)' "$xml_file")
    password=$(xmllint --xpath 'string(//Pass)' "$xml_file")
}

# Backup directory
backup_dir="sftp_backup_$(date +'%Y-%m-%d_%H-%M-%S')"
mkdir "$backup_dir"

# Read connection data from XML file
xml_file="upstream-config.xml"
read_xml "$xml_file"

# Connect to SFTP server and download all documents
echo "Downloading documents from server..."
sftp $username@$server <<EOF
get -r ./* "$backup_dir"
exit
EOF

echo "Documents downloaded and backed up to: $backup_dir"

