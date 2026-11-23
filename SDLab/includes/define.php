<?php

if ($_SERVER['REMOTE_ADDR'] === '185.25.119.62') {
    //@define('DEVMODE', true);
}

if (defined('DEVMODE') && DEVMODE === true) {
    @define('WP_POST_REVISIONS', false);
    @define('AUTOSAVE_INTERVAL', 99999);

    @define('QM_DISABLE_ERROR_HANDLER', true);
    ini_set('display_errors', 1);
    error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);
} else {
    ini_set('display_errors', 0);
    define('DISALLOW_FILE_MODS', false);
    @define('AUTOSAVE_INTERVAL', 600);
}


@define('EMPTY_TRASH_DAYS', 7);

@define('WPCF7_AUTOP', false);
