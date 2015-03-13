<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

class PushMonkeyModel {

	/**
	 * Overwrite this function to uninstall any WordPress options
	 * added/updated by this model.
	 */
	public function uninstall() {
	}
}