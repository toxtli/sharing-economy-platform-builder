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
define('DB_NAME', 'gig');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         'r!^p#0|May7)}2`>Y`k,Sa/U5g==>^lYin5W$smtWF5u0K#8r4fp@;g]fIs`~Gb1');
define('SECURE_AUTH_KEY',  'v<ie_}aXK& {N6_o}G6`JA y[u5^!Uq-&+m.O]MWq?Y8scU>|3Dz4/?7LDQrgH>X');
define('LOGGED_IN_KEY',    'bF;=acC}YsX1gmdi893qspX}lRW]6YIUIB:gKKb9@]!VVFIj5~_;b2GYXa+^bZGw');
define('NONCE_KEY',        '`XN8m?NC(/gfk>hh.)V,`g2yCgZW@jy?gR:nt![Ho8 ,==K{&(bI.[So=xR}FMIa');
define('AUTH_SALT',        '-Hp{z>=][0FD|VsMcSgi%>dnWPRve9OZ0]anSI) 4Tz(fh9*vIo,|$w]zWrif(5O');
define('SECURE_AUTH_SALT', 'XiVg]7mL.zxSsAU4pzVyzm!{FPi_ .99_Yrw&t#<}5I~k|<F9ieO/|xB@Tk-k>k7');
define('LOGGED_IN_SALT',   '^z{cVAUlto&4f gtheB{Smw8r/[ck2;%S3DuKQ4<E~;wM0^J{j>~Jja`CG70a0oN');
define('NONCE_SALT',       'T=c=`5Z^4Ge|7E[DRow)!BKoC.jjqn6 ;8s!)#>-,HcUOzfv;$3pU<,}hDu.acQ6');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
