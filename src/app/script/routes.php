<?php

/**
 * Part of The Straight Framework.
 */

/* load the Straight Framework  */
require PATH_LIB . '/Straight/straight.php';

/* English dictionary */
require PATH_I18N . '/en/en.php';

/* Define your routes like this */

/* URL: GET / */
function __do_get() {
	view( 'welcome', [ 'greeting' => dict('welcome') ] );
}

/* Handle requests */
if (!fmap( [ ], '__do' )) {

	/* No request handler has been found... */
	http_response_code(404);
	die('wut?');
}
