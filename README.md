# SPC 175

> Webiste for 175th year of St. Patrick's College, by SPC Media Unit of 2022.

Welcome to the official website of St. Patrick's College, developed by the SPC Media Unit in 2022. This website serves as a comprehensive platform to showcase the vibrant life of our institution, provide essential information, and foster communication within the St. Patrick's College community.

### Foreword to St. Patrick's College (SPC)

St. Patrick's College is a renowned educational institution committed to academic excellence, personal development, and community engagement. This website is a testament to our dedication to providing a seamless online experience for students, parents, faculty, and alumni.
## Credits

This website is made possible because of a handful of Alumni and members of SPC Media Unit of 2021, 2022 and 2023. Please visit the [Contributors](https://github.com/BirnadinErick/spc-175/graphs/contributors) to view some of them.

> I thank each and everyone and sorry I couldn't name all of you.

These contributors(not just the names in the [Contributors](https://github.com/BirnadinErick/spc-175/graphs/contributors) list, there are many!) posses the copyright to the source listing as indicated by the commit history and other means.

Gratitude to the Vice Principal's Office for letting SPC Media Unit to develop the site.

My thank to the Rector's Office for the permission to use the School's Logo and such.
## License: Apache 2.0

> [License Text](https://www.apache.org/licenses/LICENSE-2.0)

-   **Permissive License:** Users can freely use, modify, and distribute the software.
-   **Distribution Rights:** Users can distribute both original and modified versions of the software.
-   **Modification Rights:** Users can modify the source code without being required to disclose the changes.
-   **Copyright Notice:** Users must include the original copyright notice and disclaimers when distributing the software.
-   **Sublicensing:** Users can sublicense the rights granted under the license.
-   **Patent Grant:** The license includes an express grant of patent rights, providing an additional level of protection.
-   **Promotes Collaboration:** Encourages collaboration and the creation of derivative works within the open-source community.
-   **Respects User Rights:** Balances the freedom to use and modify with the requirement to maintain certain notices and disclaimers.
-   **Compatible with GPL:** The Apache 2.0 License is compatible with the GNU General Public License (GPL), allowing code under both licenses to be combined in a single project.
## Tech Stack

> This website equips an experimental approach!

When we had the final direction of the website drawn, it was definite we need an amalgamation of website & webapp. Thus the full capable version of the website is to be on v3.0.0 which will have the entire pre-determined requisites built.

The site is divided into 2 categories:

-   website
    -   houses the information for most of the people
    -   news, academics, photos
-   webapp
    -   a portal for Alumni
    -   user authentication for comments etc.

I initiated the project with a hope that any means will rise as the time passes by. So far I have decided to design the system for the website section( which is tracked as v1.0.0 milestone).

The Alumni team which sponsors the site has let us know that the site will initially on a shared hosting platform. So PHP it is. Frontend is to be chosen accordingly. At the time of writing -- HTMX is on fire, and I decided the Hypermedia-hype will be useful for this project as well.
## How it all works?

> Section intended for maintainers.

The site works with one principle in mind: Apache 2 returns `index.*` when the
path doesn't provide any filename. So when a user requests `/auth/login` to
the server, it then tries whether `auth/login.*` is available and if not returns
`index.*` in `/auth/login` directory.

Fortunately, Astro framework's default build flow does this. Thusm we develop a
page like a normal server page and Astro will handle flatening it for us.

On the other hand, backend is fully in-house grown. I have put together a small
footprint custom framework. This framework is not yet documented, nor complete.
It is more of a _metaframework_! Read it's documentation for more.
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
root `push_upstream.sh`. 

A requirement other than having a bash-compliant interpreter in your local
machine is to have the proper credentials in same directory level as the script
for successful run. See [Obtaining FTP credentails](#ftp-credentials).
## Backup Upstream

Before each push please backup the state of the server. At this commit, the
process is simply download the contents of `htdocs` of the server via SFTP
protocol.

For now backups are not stored in remote location. They reside in local computer
of the person(FTP User) who executed the script `backup-upstream.sh`! This will
later be stored in private repos/beta-server storage and cold store of School's
SPC Media Unit Data Storage Drives.

> For now this is enough, I guess!
## FTP Credentials

If you are thinking about pushing to the upstream servers for either _beta_
or _production_, please ask me(Birnadin Erick) via any means for `upstream-config.xml`
file needed for both `push-upstream.sh` and `backup-upstream.sh`.

This file is unique for you and grants you read/write access via SFTP protocol.
You will be prompted for password, which is also unique to each file. **Make sure
you do not commit these files or their contents to the version control history**.

> By accepting a designated file, you take on responsibility for any actions
> executed via these credentials.
# Content Authoring

> A Superadmin should assign a user as `EDITOR` is IAM Dashboard before one can author contents

### Steps

1. Login in to your account
2. Go to `Author Dashboard` by clicking *Manage Contents* in lower-banner
![[Pasted image 20240625104102.png]]
3. If you need to create new page contents, click on *Create*, else select the path of the content that you wish to edit in dropdown and select *Edit*
![[Pasted image 20240625104257.png]]
4. The editor is insipired by Notion. The content is not representative of final render!![[Pasted image 20240625104510.png]]

> Choose `/author/2` to an example content. The render is on path  [/Auhtor/2](https://www.spcjaffna-beta.org/author/2/)

![[Pasted image 20240625104641.png]]

Should you have any doubt, please contact [Me(Birnadin Erick)](hi@methebe.com) or you supervising SPC Media Unit President or Mentor or Member.## SPC CDN 

> CDN: Content Delivery Network

We utilize a workaround to have proper performance CDN. This includes a special workflow.Why?

- budget for this project doesn't include room for a CDN
- backend is PHP and in shared hosting. The fact our server have limited memory and no scalability!
- this will take the stress off of main servers
- availability. CDN provider will make sure contents are available no matter what

> Is this legal? Shortly, yes. Since we don't use this for profit and our school us non-profit (it should be ;] ) I hope we won't break any T&C of underlying providers.

### The Architecture

I decided to utilize the generous offer of [JsDelivr](https://www.jsdelivr.com) [GH feature](https://www.jsdelivr.com/documentation#id-github) and [GitHub](https://github.com/) offer of [unlimited space for Public repos](https://github.com/pricing#compare-features:~:text=at%20GitHub.com.-,Unlimited,-Unlimited). We host the files in [CDN Repo](https://github.com/BirnadinErick/spc-cdn) and request files from frontend using JsDelivr service (e.g. just like any npm packages). The reason for using JsDelivr is backward compatibility and thier not-complex yet `ok` [infrastructre](https://www.jsdelivr.com/network/infographic)

![](Pasted%20image%2020240626113536.png)

> Their origin servers are based in **Germany**, so GDPR compliant as well (not a legal advise)

### Uploading an Image

Throughout dashboards of the app, images are requested in either `dataURI` (*base64*-encode) or `CDNURL` form. This section explains the `CDNURL` form.

1. Acquire **write** permission to CDN Repo from [me](hi@methebe.com) or SPC Media Unit Mentor.
2. Fork the repository.![](Pasted%20image%2020240626113720.png)
3. Upload new files![](Pasted%20image%2020240626113804.png)
5. Commit the changes![](Pasted%20image%2020240626113850.png)![](Pasted%20image%2020240626114056.png)![](Pasted%20image%2020240626114146.png)
6. Send a pull request![](Pasted%20image%2020240626114205.png)

Now that the images are in the public repo, go to any editor page. An image widget is provided for you. Type in the filename of the image. Select from the dropdow menu and click on `Create Endpoint` button.

The image will be opened in a new tab/window. Copy the URL from the browser address bar or copy the image and paste it directly into the editor.