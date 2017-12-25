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
define('DB_NAME', 'u0420804_wp225');

/** MySQL database username */
define('DB_USER', 'u0420804_wp225');

/** MySQL database password */
define('DB_PASSWORD', '9Sp)1[d0Hd');

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
define('AUTH_KEY',         'enx9fhj4d3uf73kyc2r44vcuhxuyxj4z5bokpv53mgcvnwiq6yfwwb1w7hn2kx0s');
define('SECURE_AUTH_KEY',  'xzc86ywjdfjxo7ywjrjd9mx2ilznt1yat7bnlczk3rjxhshsqndc3kugzwn3it82');
define('LOGGED_IN_KEY',    '3qobjfddvi2emkkbaemntxlfa11ytxjr5kbwrr8axrhovugjkcitjoqz5qbr9ezl');
define('NONCE_KEY',        'czpgoxhtc3htwmqdoq4odhfhfb7ucotrjvksk0f9rwgkoenohokezbyxfqtehrwz');
define('AUTH_SALT',        'cojmt9bs2ctl3czi6xgpsr2cz1eldueqmfiqtmnoxdyxprrgnwltr30dejjnemdc');
define('SECURE_AUTH_SALT', '1ej9ljzuo2fapyftvvnr1aqts4zsinwlcmtqfc58y3pw5ryidaxdrxif4ajhkbs4');
define('LOGGED_IN_SALT',   'f0xxp7djibycs1c5sgiadq2qpvn5yofxevbdzdfq4udziugdrmxusp8eg0qmrsoa');
define('NONCE_SALT',       'uw2dbcew8lflqf9caln6w1qxp87kssmzkkyttpqgog3sa4s3llcar0wkzjhfgpcp');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp7o_';

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
