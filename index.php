<?php
/**
 * ID: upload_memory_limit
 * Name: Upload/memory limit
 * Description: Imposta il limite di memoria direttamente nel file htaccess
 * Icon: dashicons-upload
 * Version: 1.0
 * 
 */


class bcumlSettings {
	private $bc_uml_settings_options;
	private $bc_uml_enable_php_value;

	public function __construct() {	}

    public function load_setting_page(){
		add_action( 'admin_menu', array( $this, 'bc_uml_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'bc_uml_settings_page_init' ) );
		
    }

	public function bc_uml_settings_add_plugin_page() {
		add_submenu_page(
            'bweb-component',
			'Upload/memory limit', // page_title
			'Upload/memory limit', // menu_title
			'manage_options', // capability
			'upload_memory_limit', // menu_slug
			array( $this, 'bc_uml_settings_create_admin_page' ) // function
		);
        
	}

	public function bc_uml_settings_create_admin_page() {
		$this->bc_uml_enable_php_value = get_option( 'bc_uml_php_value' ); 
		
		?>

		<div class="wrap bc_settings_table">
			<h2 class="wp-heading-inline">Upload/memory limit</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<div class="table_php_value">
					<?php
					settings_fields( 'bc_uml_php_value_group' );
					do_settings_sections( 'bweb-component-php-value' );
					submit_button();
					?>
				</div>
			</form>
		</div>
	<?php }

	public function bc_uml_settings_page_init() {
		
		register_setting(
			'bc_uml_php_value_group', // option_group
			'bc_uml_php_value', // option_name
			array( $this, 'bc_uml_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'bc_uml_php_value_section', // id
			'', // title
			function(){echo 'Attenzione! Se imposti valori errati potresti non accedere al sito.<br>Lasciando i campi vuoti torneranno i valori di default.';}, // callback
			'bweb-component-php-value' // page
		);
		
		add_settings_field(
			'enable_php_value', // id
			'Abilita modifica', // title
			array($this,'enable_php_value_setting'), // callback
			'bweb-component-php-value', // page
			'bc_uml_php_value_section', // section
		);

		add_settings_field(
			'upload_max_filesize_setting', // id
			'upload_max_filesize', // title
			array($this,'upload_max_filesize_setting'), // callback
			'bweb-component-php-value', // page
			'bc_uml_php_value_section', // section
		);

		add_settings_field(
			'post_max_size_setting', // id
			'post_max_size', // title
			array($this,'post_max_size_setting'), // callback
			'bweb-component-php-value', // page
			'bc_uml_php_value_section', // section
		);

		add_settings_field(
			'max_execution_time_setting', // id
			'max_execution_time', // title
			array($this,'max_execution_time_setting'), // callback
			'bweb-component-php-value', // page
			'bc_uml_php_value_section', // section
		);
	}

	public function bc_uml_sanitize($input){
        
		if ( is_multisite() ) {
			// I'm not going to go here
		} else {
			// Ensure get_home_path() is declared.
			if($_REQUEST['enable_php_value']=='true'){
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			
				$home_path     = get_home_path();
				$htaccess_file = $home_path . '.htaccess';
			
				$lines = array();
				if(!empty($_REQUEST['upload_max_filesize']))
					array_push($lines,'php_value upload_max_filesize '.$_REQUEST['upload_max_filesize']);
				if(!empty($_REQUEST['post_max_size']))
					array_push($lines,'php_value post_max_size '.$_REQUEST['post_max_size']);
				if(!empty($_REQUEST['max_execution_time']))
					array_push($lines,'php_value max_execution_time '.$_REQUEST['max_execution_time']);
		
				if ( insert_with_markers( $htaccess_file, 'BEGIN BWEB_COMPONENT upload-memory-limit', $lines ) ) {
					// Celebrate your success
				} else {
					// Deal with your failure
				}
			}
		}
		return $input;
	}

	
	public function enable_php_value_setting() {
		printf(
			'<input type="checkbox" name="enable_php_value" value="true" >',
		);
	}

	public function upload_max_filesize_setting() {
		printf(
			'<input type="text" name="upload_max_filesize" value="%s" disabled>',
			ini_get( 'upload_max_filesize' )
		);
		
	}
	public function post_max_size_setting() {
		
		printf(
			'<input type="text" name="post_max_size" value="%s" disabled>',
			ini_get( 'post_max_size' )
		);
		
	}
	public function max_execution_time_setting() {
		
		printf(
			'<input type="text" name="max_execution_time" value="%s" disabled>',
			ini_get( 'max_execution_time' )
		);
	}



}
if ( is_admin() ):
	$bc_uml = new bcumlSettings();
    $bc_uml->load_setting_page();
endif;


