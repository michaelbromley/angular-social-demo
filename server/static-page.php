<?php
/**
 * This file creates a static page for crawlers such as Facebook.
 *
 * Created by PhpStorm.
 * User: Michael
 * Date: 12/05/14
 * Time: 14:31
 */

$SITE_ROOT = "http://www.michaelbromley.co.uk/experiments/angular-social-demo/";


$jsonData = getData($SITE_ROOT);
makePage($jsonData, $SITE_ROOT);


function getData($siteRoot) {
    $id = ctype_digit($_GET['id']) ? $_GET['id'] : 1;
    $rawData = file_get_contents($siteRoot.'api/'.$id);
    return json_decode($rawData);
}

function makePage($data, $siteRoot) {
    $imageUrl = $siteRoot . $data->image;
    $pageUrl = $siteRoot . "albums/" . $data->id;
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">

        <title><?php echo $data->title; ?></title>

        <!-- Twitter summary card metadata -->
        <meta property="twitter:card" content="summary" />
        <meta property="twitter:site" content="@michlbrmly" />
        <meta property="twitter:title" content="<?php echo $data->title; ?>" />
        <meta property="twitter:description" content="<?php echo $data->description; ?>" />
        <meta property="twitter:image" content="<?php echo $imageUrl; ?>" />
        <meta property="twitter:url" content="<?php echo $pageUrl; ?>" />

        <!-- Facebook open graph metadata -->
        <meta property="og:title" content="<?php echo $data->title; ?>" />
        <meta property="og:site_name " content="My Favourite Albums" />
        <meta property="og:url " content="<?php echo $pageUrl; ?>" />
        <meta property="og:description" content="<?php echo $data->description; ?>" />
        <meta property="og:image" content="<?php echo $imageUrl; ?>" />

    </head>
    <body>
    <p><?php echo $data->description; ?></p>
    <pre>
        <?php print_r($_SERVER); ?>
    </pre>
    <footer class="footer">
    </footer>
    </body>
    </html>
<?php
}
