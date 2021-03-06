<?php

// Include Beans
require_once( get_template_directory() . '/lib/init.php' );

/* Helpers and utility functions */
require_once 'include/helpers.php';

// Remove Beans Default Styling
remove_theme_support( 'beans-default-styling' );


// Enqueue uikit assets
beans_add_smart_action( 'beans_uikit_enqueue_scripts', 'human_droid_enqueue_uikit_assets', 5 );

function human_droid_enqueue_uikit_assets() {

	// Enqueue uikit overwrite theme folder
	beans_uikit_enqueue_theme( 'human-droid', get_stylesheet_directory_uri() . '/assets/less/uikit' );

	// Enuque uikit overlay component
	beans_uikit_enqueue_components( array( 'overlay' ) );

	// Add the theme style as a uikit fragment to have access to all the variables
	beans_compiler_add_fragment( 'uikit', get_stylesheet_directory_uri() . '/assets/less/style.less', 'less' );

	$behance_key = get_theme_mod( 'human_droid_behance_api_key', '' );
  if( !empty($behance_key) ) {
		// Add custom JavaScript files for theme
		beans_compiler_add_fragment( 'uikit', get_stylesheet_directory_uri() . '/assets/js/handlebars.min.js', 'js' );
	}

}


//Setup Theme
beans_add_smart_action( 'init', 'human_droid_init' );

function human_droid_init() {

	// Remove page post type comment support
	remove_post_type_support( 'page', 'comments' );
	// Register additional menus, we already have a Primary menu registered
	register_nav_menu('social-menu', __( 'Social Menu', 'human-droid'));
}


// Default one column layout.
add_filter( 'beans_default_layout', 'human_droid_default_layout' );

function human_droid_default_layout() {

    return 'c';

}

// Setup document fragements, markups and attributes
beans_add_smart_action( 'wp', 'human_droid_setup_document' );

function human_droid_setup_document() {
	// Remove the site title and site title tag.
	beans_remove_action( 'beans_site_branding' );

	// Navigation menu
	// Remove default styling and uk-navbar properties
  beans_remove_attribute( 'beans_primary_menu', 'class', 'uk-navbar' );
	beans_remove_attribute( 'beans_primary_menu', 'class', 'uk-float-right' );
  beans_remove_attribute('beans_menu[_navbar][_primary]', 'class', 'uk-navbar-nav');
	beans_remove_attribute('beans_menu[_navbar][_primary]', 'class', 'uk-visible-large ');
	//Remove offcanvas menu
	beans_remove_action('beans_primary_menu_offcanvas_button');

	// Remove Breadcrumb
	beans_remove_action( 'beans_breadcrumb' );

	//Enclose main content in panel
	beans_remove_attribute('beans_main_grid', 'class', 'uk-grid');
	beans_add_attribute('beans_main_grid', 'class', 'uk-panel uk-panel-box uk-panel-space');

	//Post meta
	beans_add_attribute( 'beans_post_meta_author', 'class', 'uk-text-muted' );
	beans_add_attribute( 'beans_post_meta_date', 'class', 'uk-text-muted' );
	beans_add_attribute( 'beans_post_meta_comments', 'class', 'uk-text-muted' );

	if ( is_user_logged_in() && is_singular()) {
		beans_add_smart_action('beans_post_header_before_markup', 'human_droid_edit_link');
	}

	// Only applies to singular and not pages
 	if ( is_singular() && !is_page() ) {
 		//remove featured image
 		beans_remove_action( 'beans_post_image' );
 	}

	//comments
	beans_add_attribute('beans_moderator_badge', 'class', 'uk-badge-success');

	// Footer
	// Remove muted text, we will style on our own
	beans_remove_attribute('beans_footer_credit', 'class', 'uk-text-muted');
	// Remove floats
	beans_remove_attribute('beans_footer_credit_left', 'class', 'uk-align-medium-left');
	beans_remove_attribute('beans_footer_credit_right', 'class', 'uk-align-medium-right');
	// Put right footer in it's own line
  beans_add_attribute('beans_footer_credit_right', 'class', 'uk-display-block');
	// Center align the whole thing
	beans_add_attribute('beans_footer_credit', 'class', 'uk-text-center');

}

function human_droid_edit_link() {
	if( !is_page_template('page_profile-page.php') ) {
		edit_post_link( __( 'Edit', 'human-droid' ), '<div class="uk-margin-bottom-small uk-text-small uk-align-right"><i class="uk-icon-pencil-square-o"></i> ', '</div>' );
	}
}

function human_droid_add_nav_menu_atts( $atts, $item, $args ) {
	//check if icon class is applied to menu and apply equivalen uk-icon to nav menu link
	if(count($item->classes) >= 1) {
		if(substr($item->classes[0], 0, 5) === "icon-") {
      if(!array_key_exists('class', $atts)) {
         $atts['class'] = '';
      }     
			$atts['class'] = $atts['class'].' uk-'.$item->classes[0];
		}
	}
  return $atts;
}
add_filter( 'nav_menu_link_attributes', 'human_droid_add_nav_menu_atts', 10, 4);

// Modify beans post meta items (filter)
beans_add_smart_action( 'beans_post_meta_items', 'human_droid_post_meta_items' );

function human_droid_post_meta_items( $items ) {

	// Move meta positions
	$items['author'] = 1;
  $items['date'] = 2;

	return $items;

}


// Add avatar uikit circle class (filter)
beans_add_smart_action( 'get_avatar', 'fast_monkey_avatar' );

function fast_monkey_avatar( $output ) {

	return str_replace( "class='avatar", "class='avatar uk-border-circle", $output ) ;

}

// Modify the footer credit left text.
add_filter( 'beans_footer_credit_text_output', 'human_droid_footer_left' );

function human_droid_footer_left() {

	return '© <a href="'.get_site_url().'">'.get_bloginfo( 'name' ).'</a>';

}

// Modify the footer credit right text.
beans_add_smart_action( 'beans_footer_credit_right_text_output', 'human_droid_footer_right' );

function human_droid_footer_right() { ?>

  <a href="https://templateflip.com/themes/human-droid/" target="_blank" title="HumanDroid theme for WordPress">HumanDroid</a> theme for <a href="http://wordpress.org" target="_blank">WordPress</a>. Built-with <a href="http://www.getbeans.io/" title="Beans Framework for WordPress" target="_blank">Beans</a>.

<?php }


//Customizer fields

//Additional Header & Footer Codes (for Google Analytics)
add_action( 'init', 'human_droid_customization_fields' );
function human_droid_customization_fields() {

	$fields = array(
		array(
			'id' => 'human_droid_head_code',
			'label' => __( 'Additional Head Code', 'human-droid' ),
			'type' => 'textarea',
			'description' => 'Add additional code that goes in head here (e.g. Google Analytics code)',
			'default' => ''
		),
		array(
			'id' => 'human_droid_behance_api_key',
			'label' => __( 'Behance API Key', 'human-droid' ),
			'type' => 'text',
			'description' => 'Enter behance API key here to use the Behance profile shortcode',
			'default' => ''
		),
	);

	beans_register_wp_customize_options( $fields, 'human_droid_custom_code', array( 'title' => __( 'Custom Code', 'human-droid' ), 'priority' => 1100 ) );
}

add_action('beans_head_append_markup', 'human_droid_custom_head_code');

function human_droid_custom_head_code() {
	echo get_theme_mod( 'human_droid_head_code', '' );
}

/* Shortcodes */
require 'include/shortcodes.php';
/* Customize Jetpack */
require 'include/jetpack-custom.php';
