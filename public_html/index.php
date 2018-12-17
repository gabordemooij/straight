<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//define
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

require( PATH_SCRIPT . '/routes.php' );
