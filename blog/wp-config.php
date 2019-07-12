<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'cashback_wp633');

/** MySQL database username */
define('DB_USER', 'cashback_wp633');

/** MySQL database password */
define('DB_PASSWORD', '7S62][yp1U');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'mcgsobrxcvput8ezlkxwejr1kbx0bm7dg4uzfldwc78807ochdctgsrf1gskgdf5');
define('SECURE_AUTH_KEY',  'bisntddkojalkpwxfk9dsept7y1idvikoan6dlo0f4yxqj1iq5wbijcgobzsrxg5');
define('LOGGED_IN_KEY',    'pj5kdylia4lcvpa5jmurzsb8hblr8svsdyxczbjez3elke7ndkgga1uhdv0jmxrj');
define('NONCE_KEY',        'objbuo8gviczsocyswkf7zrob53wml6ovfi2xsenmupisj8pxc23ttuhb0addo8a');
define('AUTH_SALT',        'fsgmybyvxre1v7tzt6elf9jeq56kkteiqivda2et2esepz0kc80lbdircjnuiynp');
define('SECURE_AUTH_SALT', '1arv65douap8osbiubfcazjnecyq489exavjsnmeb14f6zh7apqn5wcnivouxtxm');
define('LOGGED_IN_SALT',   '4r76yikxrhafn6ntbubo5mduk5cviawji88cegtrremyxourhhohpfdzogvumvox');
define('NONCE_SALT',       'a7swvcfucuibunnye4wauimytwvrtvvnbqkbr4ijtiwtg6cuvnkfrk7rde7nxqnh');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpr1_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
