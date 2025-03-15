#!/bin/bash
set -e

# shellcheck disable=SC2046
export $(grep -v '^#' .ftp-env | xargs)

# shellcheck disable=SC2034
BACKUP_FILE="spc_backup_$(date +'%Y-%m-%d_%H-%M-%S').zip"

lftp -u "$FTP_USER","$FTP_PASS" "$FTP_SERVER" <<EOF
set ssl:verify-certificate no
mirror -c --exclude "api/v1/vendor|api/v2/vendor" "/" "backup"
bye
EOF