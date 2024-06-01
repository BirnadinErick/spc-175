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
