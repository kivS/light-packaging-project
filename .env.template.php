<?php

error_reporting(E_ALL);

define('SITE_URL', '');
define('DASHBOARD_URL', '');

define('UPLOADS_DIR', __DIR__ . '/uploads');

define('DB_FILE', __DIR__ . '/db.sqlite3');

define('SENTRY_DSN', '');

define('TELEGRAM_BOT_TOKEN', '');
define('TELEGRAM_MY_CHAT_ID', '');

define('SESSION_PREFIX', '');
define('SESSION_USER_UID_KEY', SESSION_PREFIX . '-user-uid');
ini_set('session.name', 'FEATHERSESSID');
ini_set('session.cookie_lifetime', 16070400);  // ~ 6 months
ini_set('session.gc_maxlifetime', 16070400);  // ~ 6 months

define('MAILGUN_API_ENDPOINT', 'https://api.mailgun.net');
define('MAILGUN_DOMAIN', '');
define('MAILGUN_API_KEY', '');
define('MAILGUN_FROM', 'Project Light Packaging <project-light-packaging@' . MAILGUN_DOMAIN . '>');

?>