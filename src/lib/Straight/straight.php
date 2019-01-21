<?php


//  the
//  _______  _______  ______    _______  ___   _______  __   __  _______ 
// |       ||       ||    _ |  |   _   ||   | |       ||  | |  ||       |
// |  _____||_     _||   | ||  |  |_|  ||   | |    ___||  |_|  ||_     _|
// | |_____   |   |  |   |_||_ |       ||   | |   | __ |       |  |   |  
// |_____  |  |   |  |    __  ||       ||   | |   ||  ||       |  |   |  
//  _____| |  |   |  |   |  | ||   _   ||   | |   |_| ||   _   |  |   |  
// |_______|  |___|  |___|  |_||__| |__||___| |_______||__| |__|  |___|  
//
// ----------------------------------------------------------------------
//                                     Gabor de Mooij © 2019 BSD Licensed

/**
 * fmap maps an HTTP request to a PHP function
 * using a set of fixed conventions.
 *
 * For example, the following request:
 * Request GET /article/22
 * 
 * Will trigger the following function:
 * __endpoint_get_article( 22 );
 * 
 * and GET /category/wine/product/merlot
 * will trigger the following function:
 * 
 * __endpoint_get_category_product( 'wine', 'merlot' )
 * 
 * Every even URL segment will be added to the dispatch function name.
 * Every odd URL segment will be added to the list of arguments.
 * 
 * Besides that funcmap allows you to:
 * - pass a series of rewrite rules to apply before dispatching
 * - pass a prefix (__endpoint) to be used
 *
 * To accomplish the mapping from HTTP request to function,
 * the function will generate a string containing the
 * name of the function to invoke along with arguments.
 * The function string is generated as follows:
 *
 * 1. apply the url replacements specified in the 1st parameter
 * 2. take the specified $prefix parameter (default is: __endpoint )
 * 3. take the HTTP Request method in lowercase (get)
 * 4. take every even URL segment
 * 5. concat 2-4 using the underscore symbol
 *
 * @param array  pattern replace rules to apply to URI
 * @param string prefix to use
 *
 * @return mixed
 */
function fmap( $rewrite_rules = [], $prefix = '__endpoint' ) {
	$parts     = [];
	$arguments = [];

	$http_request_uri = $_SERVER[ 'REQUEST_URI' ];

	/* 1. apply rewrite rules */
	foreach( $rewrite_rules as $pattern => $replacement )

	$http_request_uri = preg_replace( "#{$pattern}#u", $replacement, $http_request_uri );

	/* 2. add the prefix to the function name */
	$parts[] = $prefix;

	/* 3a. override request method if needed */
	$method = $_SERVER[ 'REQUEST_METHOD' ];
	if (isset($_POST['_tunnel'])) $method = $_POST['_tunnel'];

	/* 3b. add the lowercase request method to the function name */
	$parts[] = strtolower( $method );

	/* 4. add even segments to function name string, odd segments to argument list */
	list($http_request_uri, ) = explode('?', $http_request_uri, 2);
	$segments = explode( '/', $http_request_uri );

	foreach( $segments as $index => $segment ) {

		if ( $segment === '' ) continue;

		if ( $index % 2 )
			$parts[] = $segment;
		else
			$arguments[] = $segment;
	}

	/* remove excessive underscores */
	$func_name = rtrim( implode( '_', $parts ), '_' );

	/* if the function does not exist return false */
	if ( !function_exists( $func_name ) ) return false;
	call_user_func_array( $func_name, $arguments );
	return true;
}

/**
 * Escapes a string for use in a UTF-8 document.
 *
 * This function will escape user input for displaying on an UTF-8 encoded HTML page.
 * It's important to make sure the HTML page uses UTF-8 and not some other encoding.
 * Encoding regressions may still compromise this XSS-prevention measure.
 * 
 * Encodes:
 *
 * & (ampersand)        &amp;
 * " (double quote)     &quot;
 * ' (single quote)     &#039; (although you should use " in your HTML only!)
 * < (less than)        &lt;
 * > (greater than)     &gt;
 *
 * @param string  $data       data to be escaped and encoded for UTF-8 output
 * @param boolean $assumeUTF8 if TRUE skips UTF-8 encoding (better performance)
 *
 * @return string
 */
function esc( $data, $assumeUTF8 = false ) {

	/* encode everything in UTF-8 */
	if ( !$assumeUTF8 )
	$data = iconv("UTF-8","UTF-8//IGNORE", $data);

	/* convert all special characters for HTML document control for UTF-8 */
	$data = htmlspecialchars( $data, ENT_QUOTES, 'UTF-8' );

	/* for old MSIE */
	$data = str_replace( '`', '&#96;', $data );

	return $data;
}

/**
 * Basic view function.
 * Loads the PHP file specified by the document argument
 * and extracts the passed variables into same symbol table.
 * If a variable already exists in the symbol table, the new
 * variable will be prefixed by __var_.
 *
 * If you need a more advanced template engine, I recommend:
 * Stamp Template Engine:
 * http://stampte.com/
 *
 * This function does not attempt to modify output buffering so
 * your buffer settings will remain untouched.
 *
 * Usage:
 * view( 'article', $data );
 *
 * @param string $document document
 * @param array  $vars     variables to extract to symbol table
 */
function view( $document, $vars = array() ) {

	/* craft a path from the document name */
	$path = PATH_VIEW . "/{$document}.php";

	/* extract variables into symbol table (prefix __var_ if collide) */
	extract( $vars, EXTR_PREFIX_SAME, '__var_' );

	/* require the template file */
	require( $path );
}

/**
* Echos the passed $data JSON encoded
* with appropriate headers (application/json) and exits.
*
* Usage:
* jout( ['message' => 'this is json'] );
*
* @param array $data data to jsonify and output
*/
function jout( $data ) {
	header('Content-Type: application/json');
	die( json_encode( $data ) );
}

/**
 * Reads the file indicated by the specified path and writes it to
 * the output buffer, using the appropriate headers. If the file
 * does not exist a 404 header will be send and the script will abort.
 * Float() supports headers for jpg, jpeg, gif, png and pdf extension.
 *
 * @param string $path path to file to send to client
 *
 * @return void
 */
function flout( $path ) {
	if (!file_exists($path)) {
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	$ext = pathinfo( $path, PATHINFO_EXTENSION );
	switch( $ext ) {
		case 'jpg':
		case 'jpeg':
			header('Content-type: image/jpeg');
			break;
		case 'gif':
			header('Content-type: image/gif');
			break;
		case 'png':
			header('Content-type: image/png');
			break;
		case 'pdf':
			header('Content-type: application/pdf');
			break;
	}
	readfile( $path );
	exit;
}

/**
 * The dict() function can be used for translation
 * and configuration.
 * Basically it just translates a word from one language into another.
 *
 * Usage (define translation):
 *
 * dict( ['I got %d apples' => 'Ik heb %d appels'] );
 *
 * Usage (translate):
 *
 * dict( 'I got %d apples', [ 4 ] );
 *
 * For configuration purposes it can be used like this:
 *
 * dict( [ 'database.host' => 'localhost' ] );
 * dict( 'database.host' );
 *
 * Another example (complex, using function):
 *
 * dict(['I have %d apples'=>function($n){
 * switch( $n ) {
 *  case 0:
 *   return 'Ik heb geen appels';
 *   break;
 *  case 1:
 *   return 'Ik heb %d appel';
 *   break;
 *  default:
 *   return 'Ik heb %d appels';
 *   break;
 * }
 * }]);
 * echo dict('I have %d apples', [1]);
 * 
 * You can also use dict() for DI.
 *
 * @param string       $word word to translate
 * @param string|array translation or parameters
 *
 * @return mixed
 */
function dict( $words, $params = array() ) {

	/* create internal dictionary */
	static $dict = array();

	/* if words is an array, then add to dictionary */
	if ( is_array( $words ) ) {

		$dict = array_merge( $dict, $words );

		return;
	}

	/* if words is a string, translate! */
	$word = $words;
	
	/* does translation exist? */
	if ( array_key_exists( $word, $dict ) ) {

		$translation = $dict[$word];

		/* a translation can also be a function */
		if ( is_callable( $translation ) ) {
			$translation = call_user_func_array( $translation, $params );
		}
		
		/* if translation is something other than string return it (DI)*/
		if (!is_string($translation)) return $translation;
		
		/* return parameterized translation */
		return vsprintf( $translation, $params );
	}

	/* if no translation available, return the word */
	return vsprintf( $word, $params );
}

/**
 * The qry() function takes a query, parameters and a retrieval
 * function and returns an array with the results.
 *
 * The first time you call the qry() function you should pass
 * a PDO instance like this:
 *
 * query( new \PDO($dsn, $user, $pass, $opts) );
 *
 * This will initiate the database. Instead of a PDO object you can
 * also pass a PDO-compatible object.
 *
 * To run a query:
 *
 * query( 'SELECT * FROM book WHERE id = ?', [ $id ] );
 *
 * By default the qry() function attempts to invoke
 * fetchAll() on the resulting object. If you prefer
 * something else you can provide a callable as the third
 * parameter.
 *
 * @param string|PDO $query     SQL query to process (or PDO object)
 * @param array      $array     parameters to bind
 * @param callable   $retrieval retrieval method (default is 'fetchAll')
 *
 * @return mixed (depends on retrieval)
 */
function query( $query, $params = null, $retrieval = 'fetchAll' ) {
		static $pdo = null;
		if ( is_null( $pdo ) ) return $pdo = $query;
		if ( is_null( $params ) ) return $pdo->query($query)->$retrieval();
		$s = $pdo->prepare($query);
		$s->execute($params);
		return $s->$retrieval();
}

/**
 * Basic test function. If you don't like unit test frameworks,
 * this is probably the most basic test function possible.
 *
 * Usage:
 *
 * asrt( (1 + 1) , 2);
 *
 * @param mixed  $observed the observed value
 * @param mixed  $expected the expected value
 * @param string $note     note to display
 *
 * @return void
 */
function asrt( $observed, $expected, $note = null ) {
	static $count = 0;
	$count++;
	if (!is_null($note)) echo "$note\n";
	if ( $expected !== $observed ) {
		echo 'FAIL';
		debug_print_backtrace();
		exit;
	}
	echo "[$count]";
}
