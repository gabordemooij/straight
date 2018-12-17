<?php

/* Load the Straight from The Hague Framework Here... */
require PATH_LIB . '/Straight/straight.php';

/* English dictionary */
require PATH_I18N . '/en/en.php';

/* Define your routes like this */

/* URL: GET / */
function __do_get() {
	view( 'welcome', [ 'greeting' => dict('welcome') ] );
}

/* */
if (!fmap( [ ], '__do' )) {
	http_response_code(404);
	die('wut?');
}
