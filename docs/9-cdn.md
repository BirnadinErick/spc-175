## SPC CDN 

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