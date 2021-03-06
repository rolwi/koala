<?php

class koala_container_homework extends koala_container
{
	protected $unit;
	private $max_group_size;

	public function __construct ( $steam_object, $unit )
	{
		parent::__construct( $steam_object );
		$this->max_group_size = $steam_object->get_attribute("HOMEWORK_MAX_GROUPSIZE");
		$this->unit = $unit;
	}

	public function get_unit ()
	{
		return $this->unit;
	}

	public function get_url ()
	{
		return koala_object::get_koala_object( lms_steam::get_root_creator( $this->steam_object ) )->get_url() . "units/" . $this->get_id() . "/";
	}

	protected function get_link_path_internal ( $top_object )
	{
		$koala_creator = koala_object::get_koala_object( lms_steam::get_root_creator( $this->steam_object ) );
		$link_path = $koala_creator->get_link_path( $top_object );
		$link_path[] = array( "name" => $this->get_display_name(), "link" => $koala_creator->get_url() . "units/" . $this->get_id() . "/", "koala_obj" => $this, "obj" => $this->steam_object );
		return $link_path;
	}
	
	function accepts_object($obj){
		return false;
	}

	public function get_context_menu ( $context, $params = array() )
	{
		if ( $context !== 'units' ) return array();
		$user = lms_steam::get_current_user();
		$menu = array();
		if ( $this->steam_object->check_access_write( $user ) ) {
			
			$menu[] = array( "name" => gettext( "Preferences" ), "link" => $this->get_link_base() . "edit" );
			$menu[] = array( "name" => gettext( "Delete unit" ), "link" => $this->get_link_base() . "delete" );
			$menu[] = array( "name" => gettext( "generate points list" ), "link" => $this->get_link_base() . "points_tab");
			$menu[] = array( "name" => gettext( "New Homework" ), "link" => $this->get_link_base() . "new_homework");
		}
		if ( $this->steam_object->check_access_insert( $user ) ) { 	
		}
		return $menu;
	}

	static public function get_access_descriptions( $grp ) {
		$private = gettext('Private');
		$public = gettext('Public');
		$ret = array(
			PERMISSION_UNDEFINED => array(
				'label' =>  gettext( 'Not defined.' ),
				'summary_short' => gettext('-')
			)
		);
		if ( (string) $grp->get_attribute( 'OBJ_TYPE' ) == 'course' )
		{
			$ret += array(
				PERMISSION_PUBLIC => array(
					'label' => gettext( 'All users can read and comment the materials. Only members can upload materials.' ),
					'summary_short' => $public,
					'members' => SANCTION_INSERT,
					'steam' => SANCTION_READ | SANCTION_ANNOTATE,
				),
				PERMISSION_PUBLIC_READONLY => array(
					'label' => gettext( 'All users can read and comment the materials. Only staff members can upload materials.' ),
					'summary_short' => $public,
					'members' => 0,
					'steam' => SANCTION_READ | SANCTION_ANNOTATE,
				),
				PERMISSION_PRIVATE => array(
					'label' => gettext( 'Only members can read, comment and upload materials.' ),
					'summary_short' => $private,
					'members' => SANCTION_READ | SANCTION_ANNOTATE | SANCTION_INSERT,
					'steam' => 0,
				),
				PERMISSION_PRIVATE_READONLY => array(
					'label' => gettext( 'Only members can read and comment. Only staff members can upload materials.' ),
					'summary_short' => $private,
					'members' => SANCTION_READ | SANCTION_ANNOTATE,
					'steam' => 0,
				)
			);
		}
		return $ret;
	}
}

?>
