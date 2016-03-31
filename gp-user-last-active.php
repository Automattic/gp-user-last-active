<?php

class GP_User_Last_Active {

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

	public function init() {
		$this->actions = apply_filters( 'gp_user_last_active_actions', $this->actions );
		add_action( 'gp_after_request', array( $this, 'after_request' ), 10, 2 );
	}

	public function update_last_active() {
		update_user_meta( get_current_user_id(), 'gp_last_active', GP::$permission->now_in_mysql_format() );
	}

	public function get_last_active( $user_id ) {
		return get_user_meta( $user_id, 'gp_last_active', true );
	}

	public function after_request( $route, $method ) {
		if ( ! ( isset( $this->actions[ $route ] ) && in_array( $method, $this->actions[ $route ] ) ) ) {
			return;
		}

		if ( ! get_current_user_id()  ) {
			return;
		}
		$this->update_last_active();
	}
}

function gp_get_user_last_active( $user_id ) {
	global $gp_user_last_active;
	return $gp_user_last_active->get_last_active( $user_id );

}

$gp_user_last_active = new GP_User_Last_Active;
add_action( 'gp_init', array( $gp_user_last_active, 'init' ) );
