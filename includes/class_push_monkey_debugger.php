<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

class PushMonkeyDebugger { 

	public function debug( $text ) {

		error_log( "=========" . $text);
	}
}