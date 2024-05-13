## Backup Upstream

Before each push please backup the state of the server. At this commit, the
process is simply download the contents of `htdocs` of the server via SFTP
protocol.

For now backups are not stored in remote location. They reside in local computer
of the person(FTP User) who executed the script `backup-upstream.sh`! This will
later be stored in private repos/beta-server storage and cold store of School's
SPC Media Unit Data Storage Drives.

> For now this is enough, I guess!
