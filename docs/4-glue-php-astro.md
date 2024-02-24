## How glue works?

As discussed above, the project incoperates both PHP and astro.
I accomplished this with `.htaccess` and playing with *Apache2* 
configuration.

As of this commit(check file's commit hash), the project requires
a special directory structure of the webroot...

- a directory with name `/api` and contains all the PHP stuff
- root directory is then populated with astro build artifacts.

> refer .htaccess file for logic of the **glue**