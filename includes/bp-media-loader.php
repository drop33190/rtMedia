<?php
/**
 * 
 */

//Exit if accessed directlly.
if ( !defined( 'ABSPATH' ) ) exit;


//Constants
define( 'BP_MEDIA_SLUG', 'media' );
define( 'BP_MEDIA_IMAGES_SLUG' , 'images' );
define( 'BP_MEDIA_VIDEOS_SLUG' , 'videos' );
define( 'BP_MEDIA_AUDIO_SLUG' , 'audio' );
define( 'BP_MEDIA_UPLOAD_SLUG' , 'upload' );


//To set the language according to the locale selected and availability of the language file.
if ( file_exists( BP_MEDIA_PLUGIN_DIR . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-media', BP_MEDIA_PLUGIN_DIR . '/languages/' . get_locale() . '.mo' );

class BP_Media_Component extends BP_Component {
	function __construct() {
		global $bp;
		
		parent::start('media', __('Media','bp-media'), BP_MEDIA_PLUGIN_DIR);
		
		$this->includes();
		
		$bp->active_components[$this->id] = '1';
		
		add_action( 'init', array( &$this, 'register_post_types' ) );
	}
	
	function includes() {
		
		$includes=array(
			'includes/bp-media-screens.php'
		);
		
		parent::includes($includes);
	}
	
	function setup_globals() {
		global $bp;
		$globals = array(
			'slug'                  => BP_MEDIA_SLUG,
			'root_slug'             => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : BP_MEDIA_SLUG,
			//'has_directory'         => true, // Set to false if not required
			'search_string'         => __( 'Search Media...', 'bp-media' ),
		);
		parent::setup_globals( $globals );
	}
	
	function setup_nav() {
		// Add 'Media' to the main navigation
		$main_nav = array(
			'name' 		      => __( 'Media', 'bp-media' ),
			'slug' 		      => BP_MEDIA_SLUG,
			'position' 	      => 80,
			'screen_function'     => 'bp_media_images_screen',
			'default_subnav_slug' => BP_MEDIA_IMAGES_SLUG
		);


		
		$sub_nav[] = array(
			'name'            =>  __( 'Upload', 'bp-media' ),
			'slug'            => BP_MEDIA_UPLOAD_SLUG,
			'parent_url'      => trailingslashit(bp_displayed_user_domain().BP_MEDIA_SLUG),
			'parent_slug'     => BP_MEDIA_SLUG,
			'screen_function' => 'bp_media_upload_screen',
			'position'        => 10
		);



		parent::setup_nav( $main_nav , $sub_nav);

		
		bp_core_new_nav_item(array(
			'name'				=>	__('Images','bp-media'),
			'slug'				=>	BP_MEDIA_IMAGES_SLUG,
			'screen_function'	=>	'bp_media_images_screen'			
			));
		bp_core_new_nav_item(array(
			'name'				=>	__('Videos','bp-media'),
			'slug'				=>	BP_MEDIA_VIDEOS_SLUG,
			'screen_function'	=>	'bp_media_videos_screen'			
			));
		bp_core_new_nav_item(array(
			'name'				=>	__('Audio','bp-media'),
			'slug'				=>	BP_MEDIA_AUDIO_SLUG,
			'screen_function'	=>	'bp_media_videos_screen'			
			));
//		bp_core_new_subnav_item( array(
//			'name' 		  => __( 'Images', 'bp-media' ),
//			'slug' 		  => BP_MEDIA_IMAGES_SLUG,
//			'parent_slug'     => bp_get_loggedin_user_username(),
//			'parent_url' 	  => trailingslashit( bp_loggedin_user_domain()),
//			'screen_function' => 'bp_media_images_screen',
//			'position' 	  => 40,
//			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
//		) );
	}
	
}


function bp_media_load_core_component() {
	global $bp;

	$bp->{BP_MEDIA_SLUG} = new BP_Media_Component();
}
add_action( 'bp_loaded', 'bp_media_load_core_component' );

function bp_media_custom_nav() {
	global $bp;
	foreach($bp->bp_nav as $key=>$nav_item) {
		if($nav_item['slug']==BP_MEDIA_IMAGES_SLUG||$nav_item['slug']==BP_MEDIA_VIDEOS_SLUG||$nav_item['slug']==BP_MEDIA_AUDIO_SLUG) {
			$bp->bp_options_nav[BP_MEDIA_SLUG][]=array(
				'name'	=>	$nav_item['name'],
				'link'	=>	$nav_item['link'],
				'slug'	=>	$nav_item['slug'],
				'css_id'	=>	$nav_item['css_id'],
				'position'	=>	$nav_item['position'],
				'screen_function'	=>	$nav_item['screen_function'],
				'user_has_access'	=>	true,
			);
			unset($bp->bp_nav[$key]);
		}
	}
	if($bp->current_component==BP_MEDIA_IMAGES_SLUG||$bp->current_component==BP_MEDIA_VIDEOS_SLUG||$bp->current_component==BP_MEDIA_AUDIO_SLUG){
		$bp->current_action=$bp->current_component;
		$bp->current_component=BP_MEDIA_SLUG;
	}
	
	
}
add_action ( 'bp_setup_nav' , 'bp_media_custom_nav' , 999 );
?>