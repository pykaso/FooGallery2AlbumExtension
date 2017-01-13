<?php
//This init class is used to add the extension to the extensions list while you are developing them.
//When the extension is added to the supported list of extensions, this file is no longer needed.

if ( !class_exists( 'Gallery2Album_Template_FooGallery_Extension_Init' ) ) {
	class Gallery2Album_Template_FooGallery_Extension_Init {

		function __construct() {
			add_filter( 'foogallery_available_extensions', array( $this, 'add_to_extensions_list' ) );
		}

		function add_to_extensions_list( $extensions ) {
			$extensions[] = array(
				'slug'=> 'gallery2album',
				'class'=> 'Gallery2Album_Template_FooGallery_Extension',
				'title'=> __('Gallery2Album', 'foogallery-gallery2album'),
				'file'=> 'foogallery-gallery2album-extension.php',
				'description'=> __('Automagically asign gallery to album.', 'foogallery-gallery2album'),
				'author'=> ' Lukas Gergel',
				'author_url'=> 'http://pykaso.net',
				'thumbnail'=> GALLERY2ALBUM_TEMPLATE_FOOGALLERY_EXTENSION_URL . '/assets/extension_bg.png',
				'tags'=> array( __('template', 'foogallery') ),	//use foogallery translations
				'categories'=> array( __('Build Your Own', 'foogallery') ), //use foogallery translations
				'source'=> 'generated'
			);

			return $extensions;
		}
	}

	new Gallery2Album_Template_FooGallery_Extension_Init();
}