#Cloud Files Gallery

###A small PHP/mySQL application for uploading and displaying image galleries using Rackspace Cloud Files.

I built this simple gallery app as an exercise to become familiar with Rackspace's PHP Open Cloud API and decided to share it in the hopes it may help somebody. This is only meant to be an example; it's not meant to be put into production on your website unless you add some integrity checks and beef up security. I'm sharing this as-is, without warranty of any kind. Use at your own risk. The code is commented and should be easy to follow. Please don't ask for help with this, I'm just way too busy to offer any meaningful support. Thanks. :)

##What It Does
* Creates photo galleries using Rackspace Cloud Files containers and a mySQL database
* Resizes and uploads images to Cloud Files containers
* Recursively deletes image galleries and containers
* Creates private and public CDN containers with temporary URLs; perfect for membership sites and digital downloads
* Uses Bootstrap 3.3, jQuery and DropZone (all via CDN)

##Requirements

* PHP >= 5.4 (+ cURL) & mySQL
* [Rackspace php-opencloud library](https://github.com/rackspace/php-opencloud) (tested with v. 1.11)

This also uses [ezSQL](https://github.com/ezSQL/ezSQL) for mySQL and [ImageMagick](http://www.imagemagick.org) for image uploads and manipulation, both of which can be easily swapped out for your own. If you're not using ImageMagick, update `assets/php/upload.php` with your own upload code.

##Installation

**Update:** [I created this companion blog post](http://timgavin.name/tuts/uploading-to-rackspace-cloud-files/), which is more detailed than the instructions below.

1. Download the `.zip` file
1. Create a new mySQL database named `open-cloud` and import the tables in `open-cloud.sql`
1. Install the [php-opencloud](https://github.com/rackspace/php-opencloud) library into `assets/plugins/`
1. Download [ezSQL](https://github.com/ezSQL/ezSQL) into `assets/plugins/` (or use your own)
1. Open `assets/config.php` and edit accordingly
1. Visit `index.php` in your browser to start creating galleries!

If you're using MAMP Pro, check out how to [easily install ImageMagick with MAMP Pro 3](http://timgavin.tumblr.com/post/115425669995/easily-install-imagemagick-with-mamp-pro).
