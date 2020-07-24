<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'cfp-getech' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Enxk89Rj`n&Z`NT6Fl.sqQ6-OK(i00@F61jAKiF^CSe=8^59*7gyht/nGl!?2K[%' );
define( 'SECURE_AUTH_KEY',  'n[~MqRN2g*T{`W|vQ3[Bm9RPV,O}^xGi N3,],v]s+vc}-[xd(!d|WF13oxq`*tZ' );
define( 'LOGGED_IN_KEY',    ')$/D!:!p:bWQIj7)Tt_S;=M&|)7@hb2[V?Q+ (03,X5^aS1x7PqLUO{AOr>v<Cw{' );
define( 'NONCE_KEY',        '8Lg{e(//iU>>SV8+/{`R#k$ZePwQ6|W^z*@=tyE$`*#q$Oh4udjQ^!im_dcZ<B1f' );
define( 'AUTH_SALT',        'I)/ZX%G_!Kbz[ 0U|4I)Or{0fb_lwOc@q/W?zM+oou75$h.ulU94#{+`24Q%L?P`' );
define( 'SECURE_AUTH_SALT', 'sI~$IHZNHljf{h^1q6.S96pyz!q$lXwqgcw%MsKmqmk$I O?]=Eb0=^<{x dfu<:' );
define( 'LOGGED_IN_SALT',   ',;+)X`g#PkGEpa:x8./=!oY.?qwWxR6lu^>P*1!Kml>EmbI5v_NGPIyD0oIvxf!F' );
define( 'NONCE_SALT',       'ZE5IpB=F|3^bU-%2#33t{_;[;MSLSh8< .N-FOdVy^:rk7e2[gO^VU9Y@|Bp` 4a' );
  define('FS_METHOD', 'direct');

  /**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
