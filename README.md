# AngularJS Social Demo

This is a project which aims to illustrate how to enable rich social media sharing of your AngularJS app. This was created to accompany a talk on the same subject I
am giving at the [Vienna AngularJS meetup on 30th June 2014](http://www.meetup.com/AngularJS-Vienna/events/186829962/)

## Background

When sharing a link on social media website such as Facebook and Twitter, the site will send a crawler to the URL being shared in order to scrape data from it to make the
link richer. The data scraped typically includes a title, description, image, and potentially much more. A common way to convey this information is via the
[Open Graph Protocol](http://ogp.me/) (used by Facebook, Pinterest, Google+). Briefly, to use it one would include special meta tags in the header of your HTML document:

```HTML
<head>
    <meta property="og:title" content="My Page" />
    <meta property="og:description" content="A description of my page." />
    <meta property="og:image" content="http://www.mysite.com/images/my_lovely_face.jpg" />
    <!-- etc. -->
</head>
```

These meta tags are then read by the crawler and the contents are used to generate a richer sharing model (e.g. by including the image and description in the Facebook timeline)

The problem with AngularJS apps is that the entire view is built by JavaScript on the client, but (as of this writing at least), the various social media crawlers do not execute JavaScript
on the target URL, and as a result they will only see the raw HTML template without any content. To the crawler, your template would look something like this:

```HTML
<head>
    <meta property="og:title" content="{{ page.title }}" />
    <meta property="og:description" content="{{ page.description }}" />
    <meta property="og:image" content="{{ page.image }}" />
    <!-- etc. -->
</head>
```

## Solution

The solution is basically to use some kind of server-side user-agent detection to pick up whenever a social media crawler arrives, and then instead of showing it the plain
AngularJS template file, redirect it to a server-generated page that will contain the desired meta tags, all filled with the correct information.

#### A working demo of the following implementation can be found [here](http://www.michaelbromley.co.uk/experiments/angular-social-demo/), and all the code is contained in this repo.

## What we will need

1. A web server capable of URL rewriting. In this case, we are using Apache and the mod_rewrite module.
2. A server-side language to generate our crawler-friendly pages. In this case I will use PHP.

Following is a write-up of how to set things up assuming the above technologies are being used.

### Configure Apache

We will need two specific Apache modules enabled: mod_rewrite and mod_proxy (installation of these modules will vary depending on your OS/Apache version, but is beyond the scope
of this article). We will come back to the use of these modules shortly.

### Set up the server-side script

Next we need to make the script that will handle the requests from the social media crawlers. Let's assume that our AngularJS app gets its data from an API. In this example,
we are getting album information from the endpoint `api/{id}`. We can re-use this same API in our server-side script and use the data to build, on the server, our
HTML page including all the social media meta tags, and output this HTML to the crawler.

An simplified PHP implementation follows:

```PHP
$SITE_ROOT = "http://www.mysite.com/";

$jsonData = getData($SITE_ROOT);
makePage($jsonData, $SITE_ROOT);


function getData($siteRoot) {
    $id = ctype_digit($_GET['id']) ? $_GET['id'] : 1;
    $rawData = file_get_contents($siteRoot.'api/'.$id);
    return json_decode($rawData);
}

function makePage($data, $siteRoot) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta property="og:title" content="<?php echo $data->title; ?>" />
        <meta property="og:description" content="<?php echo $data->description; ?>" />
        <meta property="og:image" content="<?php echo $data->image; ?>" />
        <!-- etc. -->
    </head>
    <body>
        <p><?php echo $data->description; ?></p>
        <img src="<?php echo $imageUrl; ?>">
    </body>
    </html>
<?php
}
```

The output of this script can be tested by visiting it directly in the browser. In the example, that would be
[http://www.michaelbromley.co.uk/experiments/angular-social-demo/server/static-page.php?id=1](http://www.michaelbromley.co.uk/experiments/angular-social-demo/server/static-page.php?id=1)

### Redirect crawlers to the server-side script

Now that we have our server-side script up an running, we just need to set up the redirection. This is done with an `.htaccess` file containing the following rule:

```ApacheConf
<ifModule mod_rewrite.c>
 RewriteEngine On

# allow social media crawlers to work by redirecting them to a server-rendered static version on the page
RewriteCond %{HTTP_USER_AGENT} (facebookexternalhit/[0-9]|Twitterbot|Pinterest|Google.*snippet)
RewriteRule album/(\d*)$ http://www.michaelbromley.co.uk/experiments/angular-social-demo/server/static-page.php?id=$1 [P]

</ifModule>
```

The `RewriteCond` link looks at the [user agent](http://en.wikipedia.org/wiki/User_agent) string to see if it matches the following expression. The specific strings used in this
expression are based on the known user agents of the various social media crawlers (at the time of this writing):

* Facebook: `facebookexternalhit/1.1 (+http(s)://www.facebook.com/externalhit_uatext.php)`
* Twitter: `Twitterbot/{version}`
* Pinterest: `Pinterest/0.1 +http://pinterest.com/`
* Google Plus: `Google (+https://developers.google.com/+/web/snippet/)`
* Google Structured Data Testing tool: `Google-StructuredDataTestingTool; +http://www.google.com/webmasters/tools/richsnippets`

### Test it out

Now that everything is set up, it's time to test out whether it actually works as expected. All the social media sites we have mentioned so far offer some kind of
validation tool that will give you an idea of what your URL will look like when shared:

* [Facebook Open Graph Object Debugger](https://developers.facebook.com/tools/debug/) - also useful for any other open-graph based site.
* [Twitter Card Validator](https://dev.twitter.com/docs/cards/validation/validator) - you need a twitter account to use it.
* [Pinterest Rich Pin Validator](https://developers.pinterest.com/rich_pins/validator/)
* [Google Structured Data Testing tool](http://www.google.com/webmasters/tools/richsnippets)