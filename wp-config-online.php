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
define('DB_NAME', 'sql12231900');
/* define('DB_NAME', 'laith_al_obaidi_cars'); */

/** MySQL database username */
define('DB_USER', 'sql12231900');
/* define('DB_USER', 'root'); */

/** MySQL database password */
define('DB_PASSWORD', 'TGIjk7iWtD');
/* define('DB_PASSWORD', ''); */

/** MySQL hostname */
define('DB_HOST', 'sql12.freemysqlhosting.net');
/* define('DB_HOST', 'localhost'); */

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
define('AUTH_KEY',         '<?BoCzOkvTz8$G]-Sbm+%ezo~:WoUF t3RIw1AG:H/{H{l$.(uBj`z*K$bSa=KJ:');
define('SECURE_AUTH_KEY',  '$/r8x%Z8Q].%<XET6(dDs9g+v4FG)R5%YwEKd?AY&x8g4j0L$>j-Y=-gHW0<eVt/');
define('LOGGED_IN_KEY',    'utHo;Z]&wxOU}&$G]IHwb$95-vA/w-9L2ywC:z7YFIelz ~JyD8qMng=g(9{W~;]');
define('NONCE_KEY',        ';Gt;i/G*nqIb*p}%.}T=.otB0XP2%c=6s#^%nWQpwYsnYoZ[hg35[#&l+&1|^e)S');
define('AUTH_SALT',        '}Ox9~:ArY>U>fS=a>(m& *bm52$;VDdw]_e;hUdB35`Dc8zHp1!6DaFdBQo`,#F]');
define('SECURE_AUTH_SALT', 'L?2CRI}:K(4JIY!YF`ADL<P-@b/Xn0t_U5IL^6>FHd#kxO.Vwz&v[;nb^x#Z&u76');
define('LOGGED_IN_SALT',   '/FI~$:N_~8-^Lh;JMk0@6]HO37o%kDR14X4A_)VF+&A9VQHhNe!5KrAzvT%HA1ew');
define('NONCE_SALT',       'Swa){*(,1$>2:*fJjJ44KVq{A;.)`O^hJs_7roNxL3=e+IiS|% 6:~?s_<c,q?^q');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'lao_';

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
