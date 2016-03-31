<?php

class GP_User_Last_Active extends GP_Plugin {

	var $id = 'gp_user_last_active';

	var $actions = array(
		'GP_Route_Glossary_Entry' => array(
			'glossary_entries_post',
			'glossary_entry_add_post',
			'glossary_entry_delete_post',
			'import_glossary_entries_post'
		),
		'GP_Route_Translation' => array(
			'bulk_post',
			'import_translations_post',
			'discard_warning',
			'set_status',
			'translations_post',
		)
	);

	public function __construct() {
		parent::__construct();
		$this->actions = apply_filters( 'gp_user_last_active_actions', $this->actions );
		$this->add_action( 'after_request', array( 'args' => 2 ) );
	}

	public function update_last_active() {
		GP::$user->current()->set_meta( 'gp_last_active', GP::$user->now_in_mysql_format() );
	}

	public function get_last_active( $user_id ) {
		global $gpdb;
		return $gpdb->get_var( $gpdb->prepare( "SELECT meta_value FROM $gpdb->usermeta WHERE meta_key = 'gp_last_active' AND user_id = %d", $user_id ) );
	}

	public function after_request( $route, $method ) {
		if ( ! ( isset( $this->actions[ $route ] ) && in_array( $method, $this->actions[ $route ] ) ) ) {
			return;
		}

		if ( GP::$user->current()->id == 0 ) {
			return;
		}
		$this->update_last_active();
	}
}

GP::$plugins->gp_user_last_active = new GP_User_Last_Active;
