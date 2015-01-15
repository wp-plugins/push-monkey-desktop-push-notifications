<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

class PushMonkeyDebugger { 

	public function debug( $text ) {

		error_log( "=========" . $text);
	}

	public function debug2( $prefix, $text ) {

		$output = print_r( $text, true );
		print_r( '<br />==== ' . $prefix . ': ' . $output );
	}
}