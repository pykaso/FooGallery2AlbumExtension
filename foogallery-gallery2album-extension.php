<?php
/**
 * FooGallery Gallery2Album Extension
 *
 * Automagically attach gallery to album.
 *
 * @package   Gallery2Album_Template_FooGallery_Extension
 * @author     Lukas Gergel
 * @license   GPL-2.0+
 * @link      http://pykaso.net
 * @copyright 2014  Lukas Gergel
 *
 * @wordpress-plugin
 * Plugin Name: FooGallery - Gallery2Album
 * Description: Automagically attach gallery to album.
 * Version:     1.0.0
 * Author:       Lukas Gergel
 * Author URI:  http://pykaso.net
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( !class_exists( 'Gallery2Album_Template_FooGallery_Extension' ) ) {

	define('GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_FILE', __FILE__ );
	define('GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_URL', plugin_dir_url( __FILE__ ));
	define('GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_VERSION', '1.0.0');
	define('GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_PATH', plugin_dir_path( __FILE__ ));
	define('GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_SLUG', 'foogallery-gallery2album');
	//define('GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_UPDATE_URL', 'http://fooplugins.com');
	//define('GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_UPDATE_ITEM_NAME', 'Gallery2Album');

	require_once( 'foogallery-gallery2album-init.php' );

	class Gallery2Album_Template_FooGallery_Extension {
		/**
		 * Wire up everything we need to run the extension
		 */
		function __construct() {
			add_filter( 'foogallery_gallery_templates_files', array( $this, 'register_myself' ) );
			add_action( 'foogallery_after_save_gallery', array( $this, 'attach_gallery_to_album' ), 10, 1 );
			add_filter( 'foogallery_admin_settings_override', array($this, 'add_settings' ) );

			//used for auto updates and licensing in premium extensions. Delete if not applicable
			//init licensing and update checking
			//require_once( GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_PATH . 'includes/EDD_SL_FooGallery.php' );

			//new EDD_SL_FooGallery_v1_1(
			//	GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_FILE,
			//	GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_SLUG,
			//	GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_VERSION,
			//	GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_UPDATE_URL,
			//	GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_UPDATE_ITEM_NAME,
			//	'Gallery2Album');
		}

		/**
		 * Attach saved gallery to album defined in settings
		 */
		function attach_gallery_to_album( $post_id ) {
			$album_id = foogallery_get_setting( 'gallery_default_album_id', '375' );
			if ($album_id == ''){
				return;
			}
			$gallery = FooGallery::get_by_id( $post_id );
			$album = FooGalleryAlbum::get_by_id($album_id);
			if(!$album){
				return;
			}
			if (!$album->includes_gallery($post_id)){
				$galleries = $album->gallery_ids;
				if (!is_array( $galleries ) ) {				
					$galleries = [];
				}
				array_push($galleries, $post_id);
				update_post_meta( $album_id, FOOGALLERY_ALBUM_META_GALLERIES, $galleries );	
			}
		}

		/**
		 * Returns all FooGallery albums
		 *
		 * @return FooGallery[] array of FooGallery albums
		 */
		function foogallery_get_all_albums( $excludes = false ) {
			$args = array(
				'post_type'     => FOOGALLERY_CPT_ALBUM,
				'post_status'	=> array( 'publish', 'draft' ),
				'cache_results' => false,
				'nopaging'      => true,
			);

			if ( is_array( $excludes ) ) {
				$args['post__not_in'] = $excludes;
			}

			$album_posts = get_posts( $args );

			if ( empty( $album_posts ) ) {
				return array();
			}

			$albums = array();

			foreach ( $album_posts as $post ) {
				$albums[] = FooGalleryAlbum::get( $post );
			}

			return $albums;
		}

		/**
		 * Register myself
		 * @param $extensions
		 *
		 * @return array
		 */
		function register_myself( $extensions ) {
			$extensions[] = __FILE__;
			return $extensions;
		}


		/**
		 * Register my settings
		 */
		function add_settings( $settings ) {

			$settings['tabs']['gallery2album'] = __( 'Gallery2Album', 'foogallery' );

			$albums = $this->foogallery_get_all_albums();
			$albums_choices = array();
			$albums_choices[] = __( 'Not set (disabled)', '' );
			foreach ( $albums as $album ) {
				$albums_choices[ $album->ID ] = $album->name;
			}

			$settings['settings'][] = array(
				'id'      => 'gallery_default_album_id',
				'title'   => __( 'Associated album', 'foogallery' ),
				'desc'    => __( 'Album to wchis is newly created gallery attached. If is value "Not set" auto attach feature is disabled.' ),
				'type'    => 'select',
				'choices' => $albums_choices,
				'tab'     => 'gallery2album'
			);

			return $settings;
		}
	}
}