#!/bin/bash
set -e

echo "importing env variables..."
# shellcheck disable=SC2046
export $(grep -v '^#' .ftp-env | xargs)

# shellcheck disable=SC2034
BACKUP_FILE="spc_backup_$(date +'%Y-%m-%d_%H-%M-%S').zip"
echo "backup file: $BACKUP_FILE"

echo "mirroring the site..."
lftp -u "$FTP_USER","$FTP_PASS" "$FTP_SERVER" <<EOF
set ssl:verify-certificate no
mirror -c --exclude "api/v1/vendor|api/v2/vendor|backups" "/" "backup"
bye
EOF

echo "compressing mirror..."
zip -r --encrypt "$BACKUP_FILE" ./backup/

echo "uploading the backup..."
lftp -u "$FTP_USER","$FTP_PASS" "$FTP_SERVER" <<EOF
set ssl:verify-certificate no
cd "/backups"
put "$BACKUP_FILE"
bye
EOF

echo "clean up..."
rm "$BACKUP_FILE"
rm -rf backup/

echo "Backup complete."