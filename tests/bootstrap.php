<?php
require __DIR__.'/../vendor/autoload.php';
date_default_timezone_set('UTC');

function getWordpressPath()
{
    $fileToCheck = 'wp-settings.php';
    $paths = [];

    foreach (range(1, 8) as $level) {
        $nested = str_repeat('/..', $level);
        array_push($paths, __DIR__ . "$nested/wordpress/$fileToCheck", __DIR__ . "$nested/$fileToCheck");
    }

    $filtered = array_filter($paths, function ($path) {
        return file_exists($path);
    });

    return str_replace("/$fileToCheck", '', array_shift($filtered));
}

$wp_load = getWordpressPath();

if( ! file_exists($wp_load) ) {
    echo 'PHP Unit: WordPress Not Connected at ' . $wp_load . PHP_EOL;
} else {
    define('BASE_WP', $wp_load);
    define('WP_USE_THEMES', false);
    new \TypeRocket\Core\Config( __DIR__ . '/config');

    // Disable email
    function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) { return true; }
    global $wp, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
    require BASE_WP . '/wp-load.php';
    require BASE_WP . '/wp-admin/includes/user.php';
    require BASE_WP . '/wp-admin/includes/upgrade.php';

    // Create Mock Tables
    dbDelta('CREATE TABLE `posts_terms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `terms_id` int(11) DEFAULT NULL,
  `posts_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
}
