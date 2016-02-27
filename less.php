<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
/**
 * Automatically compile LESS files
 * 
 * Features:
 *  - Uses the system temp directory to ensure it is writable
 *  - Gzip compression
 *  - Compile only if not modified
 *  - Respect If-Modified-Since header
 *  - @todo Add caching for gzipped version
 *  - @todo Add caching header
 * 
 * Installation:
 * 1. Download lessphp and extract to $DOCUMENT_ROOT/lessphp
 * @link http://leafo.net/lessphp/
 * 
 * 2. Add this script in the lessphp directory
 * 
 * 3.1. Add RewriteRule in htaccess
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} ^(.*)\.css
 * RewriteCond %1.less -f
 * RewriteRule ^(.*)\.css lessphp/less.php?f=$1.less
 * 
 * 3.2. If htaccess is not possible, replace CSS links from
 * /css/style.less
 * to
 * /lessphp/less.php?f=css/style.less
 * 
 * 
 * @link https://gist.github.com/4127137
 */
 
if (empty($_GET['f']) || !preg_match('/\.less$/', $_GET['f'])) {
	header('HTTP/1.0 400 Bad Request');
	die();
}

$tmp_dir = sys_get_temp_dir();
if (substr($tmp_dir, -1) != '/')
	$tmp_dir = $tmp_dir.'/';
$cache_dir   = $tmp_dir  . 'lessphp/' . $_SERVER['SERVER_NAME']; // will store files in /tmp/lessphp/example.com/css/style.css
$doc_root    = dirname(dirname(__FILE__));
$less_file   = "{$_GET['f']}";
$enable_gzip = false;
 

if (!is_file($less_file)) {
	header('HTTP/1.0 404 Not Found');
	die();
}
 
if (!is_dir(dirname($cache_dir))) {
	mkdir(dirname($cache_dir), 0755, true);
}
 
require 'lessc.inc.php';
 
try {
	// Compiles only if $less_file mtime != $css_file mtime
	//$less->parseFile($less_file, $css_file);
	if (isset($_GET['debug']))
		$css_file = Less_Cache::Get(array($less_file=>''), array('relativeUrls' => false, 'use_cache' => false, 'cache_dir' => $cache_dir, 'compress' => false, 'sourceMap' => true));
	else
		$css_file = Less_Cache::Get(array($less_file=>''), array('relativeUrls' => false, 'cache_dir' => $cache_dir, 'compress' => true, 'sourceMap' => false));
	$css_file = $cache_dir.'/'.$css_file;
} catch (Exception $e) {
	header('HTTP/1.0 500 Internal Server Error');
	echo $e->getMessage();
	die();
}
 
$fp = fopen($css_file, 'r');
$stat = fstat($fp);
 
if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $stat['mtime']) {
	header('HTTP/1.0 304 Not Modified');
} else {
	header('Cache-Control: must-revalidate');
	header('Content-Type: text/css; charset=utf-8');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $stat['mtime']) . ' GMT');
 
	if ($enable_gzip) {
		header('Content-Encoding: gzip');
		ob_start("ob_gzhandler");
	}
	fpassthru($fp);
}
 
fclose($fp);