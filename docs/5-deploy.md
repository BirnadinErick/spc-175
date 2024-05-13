## Deployment Guide

The sponsors of the project offered us Shared Hosting. So that was the main
drive behind choosing php (or Django/Python was in my mind). Since the service
allows for SFTP transfer, we employ it.

There are 4 steps:

1. Build frontend
2. Trnaspile backend if needed
3. Create a monolithic `app` folder
4. Sync the upstream server

_In addition, as a best practise we will backup the contents of the server right
before push to developers local machine_.

The deployment is termed as `push to upstream` in docs here and there, and the
same norm for the backup as well.

The deployment is automated using a bash script which is located in project
root `push_upstream.sh`. Execute the script after completing following steps:

-   change `DEBUG` in `src/config/global.ts` to false
-   change `DEBUG` in `api/v1/index.php` to false

A requirement other than having a bash-compliant interpreter in your local
machine is to have the proper credentials in same directory level as the script
for successful run. See [Obtaining FTP credentails](#ftp-credentials).
