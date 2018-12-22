<?php

/**
 * Part of The Straight Framework.
 */

/**
 * PATH definitions.
 *
 * Recommendation: put your system files out of the
 * web root folder (public_html).
 * ------------------------------------------------
 */
define( 'PATH_SYSTEM', '..' );
define( 'PATH_SRC',    PATH_SYSTEM .  '/src'     );
define( 'PATH_DATA',   PATH_SYSTEM .  '/data'    );
define( 'PATH_APP',    PATH_SRC    .  '/app'     );
define( 'PATH_LIB',    PATH_SRC    .  '/lib'     );
define( 'PATH_CONFIG', PATH_APP    .  '/config'  );
define( 'PATH_I18N',   PATH_APP    .  '/i18n'    );
define( 'PATH_VIEW',   PATH_APP    .  '/view'    );
define( 'PATH_MODEL',  PATH_APP    .  '/object'  );
define( 'PATH_OBJECT', PATH_APP    .  '/object'  );
define( 'PATH_SCRIPT', PATH_APP    .  '/script'  );

/**
 * Dispatch request to the routes script.
 */
require( PATH_SCRIPT . '/routes.php' );
