# EmbedBox

> Syntax-highlighted code embed generator

Written by the author of [Pepperminty Wiki](https://github.com/sbrl/Pepperminty-Wiki/), EmbedBox is a generic syntax highlighted code embed generator. Given a URL to some code, it generates a syntax-highlighted embed for it.

Note that the URL needs to be to the _raw_ code. Example:

URL										| .
----------------------------------------|------------
`https://github.com/sbrl/EmbedBox/blob/master/index.php`            | **wrong**
`https://raw.githubusercontent.com/sbrl/EmbedBox/master/index.php`  | **right**

In other words, it needs to be the URL from the <kbd>raw</kbd> button on the file, not the link to the file in GitHub / GitLab's web interface.

**Latest Version:** [![stable version badge](https://img.shields.io/github/release/sbrl/EmbedBox.svg?color=brightgreen&label=stable)](https://github.com/sbrl/EmbedBox/releases/latest) ![pre-release version badge](https://img.shields.io/github/v/release/sbrl/EmbedBox?color=yellow&include_prereleases&label=pre-release) ([Changelog](https://github.com/sbrl/EmbedBox/blob/master/Changelog.md))

Stay up-to-date! [![RSS feed](https://img.shields.io/badge/%F0%9F%8D%84-RSS-orange)](https://github.com/sbrl/EmbedBox/releases.atom) [![GitHub Releases](https://img.shields.io/badge/%E2%8E%94-GitHub%20Releases-black)](https://github.com/sbrl/EmbedBox/releases) (Don't forget you can watch releases too) 


## Contents
 - [Features](#features)
     - [Screenshots](#screenshots)
 - [Demo](#demo)
 - [System Requirements](#system-requirements)
 - [Installation](#installation)
 - [Configuration](#configuration)
 - [Real World Usage](#real-world-usage)
 - [Contributing](#contributing)
 - [Licence](#licence)


## Features
 - Server-side syntax highlighting
 - [Inbuilt cache](http://www.stashphp.com/) with stampede protection (defaults to 1 hour)
 - [`prefers-color-scheme`](https://starbeamrainbowlabs.com/blog/article.php?article=posts%2F353-prefers-color-scheme.html) support
 - Multiple themes
 - Javascript-free client-side (embed only - the interactive builder needs JS)
 - Should be fairly accessible (screen readers, ACAG accessible text standards - except some minor issue with the syntax highlighting itself) - [open an issue](https://github.com/sbrl/EmbedBox/issues/new) if you find a problem
 - Can be hosted in a subdirectory
 - Regular expression-based allow / deny rules for URLs


### Screenshots
<details>
<summary>Expand to view screenshots</summary>
<h4>The embed builder</h4>
<img alt="Screenshot of the builder" src="https://raw.githubusercontent.com/sbrl/EmbedBox/master/screenshot.png" />
<h4>A full-screen embed</h4>
<img alt="Screenshot of a full-screen embed" src="https://raw.githubusercontent.com/sbrl/EmbedBox/master/screenshot2.png" />
</details>


## Demo
Check out the demo here: <https://starbeamrainbowlabs.com/embedbox/>

It will only work for code in my GitHub / GitLab repositories (i.e. repositories under <https://github.com/sbrl/> or <https://gitlab.com/sbrl/>) to discourage hotlinking (see below for instructions to run your own instance!).


## System Requirements
 - [PHP](https://php.net/)-enabled web server with a recent version of PHP and write access
 - [Composer](https://getcomposer.org/)
 - [git](https://git-scm.com/)


## Installation
First, clone this repository:

```bash
git clone https://github.com/sbrl/EmbedBox.git;
```

Then, install dependencies:

```
cd EmbedBox;
composer install;
```



### Configuration
After you've made at least 1 request to it, the custom settings file will be created at `data/settings.toml`. Open this up for editing in your favourite editor.

There are 2 settings that need to be adjusted.

The first of these is the `root_url` in the `[http]` section. This needs to be the absolute external path to `index.php` as an external user would see it. This is used when generating the embed HTML in the `<iframe>`'s `src`.

The second of these is the `allow` array in the `[access_control]` section. This should be a list of regular expressions. If a given URL matches one of these regular expressions, it will be allowed. If a URL does _not_ match any of these regular expressions, it will be blocked. This helps avoid use by others rendering embeds for other random stuff, eating up your server resources.

For example, here's a pair of regular expressions that match against your GitHub / GitLab content:

```toml
[access_control]
allow = [
	"^https:\\/\\/git(hub|lab)\\.com\\/YOUR_USERNAME_HERE\\/.*$",
	"^https:\\/\\/((gist|raw)\\.)?githubusercontent.com\\/YOUR_USERNAME_HERE\\/.*$",
]
```

Replace `YOUR_USERNAME_HERE` with your GitHub / GitLab username.

Save and close the file. You're done! You should now be able to use EmbedBox. Navigate to the `root_url` you set in the configuration file in your web browser to get started.

Advanced users can take advantage of [additional configuration options](https://github.com/sbrl/EmbedBox/blob/master/src/settings.default.toml) to customise their instance.


## Real World Usage
 - On my blog
 - _(Are you using EmbedBox? [Open an issue](https://github.com/sbrl/EmbedBox/issues/new) and I'll feature you here!)_


## Contributing
Contributions are very welcome - both issues and pull requests! Please mention in your pull request that you release your work under the MPL-2.0 (see below).

If you're feeling that way inclined, the sponsor button at the top of the page (if you're on GitHub) will take you to my Liberapay profile if you'd like to donate to say an extra thank you :-)


## Licence
EmbedBox is released under the Mozilla Public License 2.0. The full license text is included in the `LICENSE` file in this repository. Tldr legal have a [great summary](https://tldrlegal.com/license/mozilla-public-license-2.0-(mpl-2)) of the license if you're interested.

The icon however is licenced under the [Creative Commons Share-Alike Attribution 4.0 International Lience](https://creativecommons.org/licenses/by-sa/4.0/) (CC-BY-SA 4.0).
