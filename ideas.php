<?php 
/*
Plugin Name: Ideas
Plugin URI: http://heyitsgeorge.com/plugins/ideas
Description: A super simple way to keep track of ideas in your WordPress admin.
Version: 1.0
Author: Hey, It's George
Author URI: http://heyitsgeorge.com/
Text Domain: ideas
.
Light bulb icon from http://randyjensenonline.com/thoughts/wordpress-custom-post-type-fugue-icons/
.
Copyright 2012 Hey It's George, LLC  (email : wp@heyitsgeorge.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 *  Definitions
 *  @since 1.0
 */
if (! defined('IDEAS_URL') ) :

    define('IDEAS_URL', plugins_url( '' ,  __FILE__ ) );

endif;

/**
 *  Text domain
 *  @since 1.0
 */
load_plugin_textdomain('ideas', false, basename( dirname( __FILE__ ) ) . '/languages' );

/**
 *  Register "idea" post type
 *  @since 1.0
 */
function ideas_register_post_type() {

    $ideas_args = array(
        'labels' => array(
            'name' => __('Ideas', 'ideas'),
            'description' => __('A place to keep of track of little ideas that pop up here and there.', 'ideas'),
            'singular_name' => __('Idea', 'ideas'),
            'add_new' => __('Add Idea', 'ideas'),
            'add_new_item' => __('Add Idea', 'ideas'),
            'edit_item' => __('Edit Idea', 'ideas'),
            'new_item' => __('New Idea', 'ideas'),
            'view_item' => __('Explore Idea', 'ideas'),
            'search_items' => __('Search Ideas', 'ideas'),
            'not_found' => __('Nothing Found', 'ideas'),
            'not_found_in_trash' => __('Nothing Found in Trash', 'ideas')
        ),
        'supports' => array(
            'title',
            'revisions',
            'custom-fields'
        ),
        'rewrite' => array(
            'slug' => 'ideas'
        ),
        'query_var' => 'ideas',
        'public' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'hierarchical' => true, // could be helpful to "nest" ideas. will build this out
        'has_archive' => true,
        'menu_position' => 5,
        'menu_icon' => ''. IDEAS_URL . '/img/light-bulb.png'
    );

    register_post_type('idea', $ideas_args);

}



/**
 *  Register meta boxes. added in ideas_init.
 *  @since 1.0
 */
function ideas_add_meta_boxes() {

    add_meta_box(
        'ideas_content_meta_box', 
        __('What are you thinking?'), 
        'ideas_build_meta_boxes', // line 106 
        'idea', 
        'normal', 
        'high'
    );

}



/**
 *  Output for meta boxes in post editor
 *  @since 1.0
 */
function ideas_build_meta_boxes( $post ) {

    $ideas_content_text = get_post_meta( $post->ID, 'ideas_content_meta_box', true );  
    $ideas_links_text = get_post_meta( $post->ID, 'ideas_links_meta_box', true );  

    wp_nonce_field( 'ideas_meta_box_nonce', 'meta_box_nonce' );
    
    ?>

    <!-- Content -->
    <p>
        <label class="screen-reader-text" for="ideas_content_meta_box"><?php _e('Explain your idea here.', 'ideas'); ?></label>
        <textarea class="widefat" rows="5" name="ideas_content_meta_box" id="ideas_content_meta_box"><?php echo $ideas_content_text; ?></textarea>
    </p>

    <!-- Links -->
    <p>
        <label class="screen-reader-text" for="ideas_links_meta_box"><?php _e('Save any links here for later (optional).', 'ideas'); ?></label>
        <textarea class="widefat" rows="5" name="ideas_links_meta_box" id="ideas_links_meta_box"><?php echo $ideas_links_text; ?></textarea>
    </p>

    <?php  

}



/**
 *  Save our metabox values
 *  @since 1.0
 */
function ideas_meta_box_save( $post_id ) {

    // checks
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'ideas_meta_box_nonce' ) ) return;
    if( !current_user_can( 'edit_post' ) ) return;
    
    // only allow <a> tags with href attr
    $ideas_allowed_tags = array('a' => array('href' => array()));
    
    if( isset( $_POST['ideas_content_meta_box'] ) )
        update_post_meta( $post_id, 'ideas_content_meta_box', wp_kses( $_POST['ideas_content_meta_box'], $ideas_allowed_tags ) );
        
    if( isset( $_POST['ideas_links_meta_box'] ) )
        update_post_meta( $post_id, 'ideas_links_meta_box', wp_kses( $_POST['ideas_links_meta_box'], $ideas_allowed_tags ) );


}
add_action( 'save_post', 'ideas_meta_box_save' );



/**
 *  Custom meta links
 *  @link http://thematosoup.com/development/add-action-meta-links-wordpress-plugins/
 *  @since 1.0
 */
function ideas_meta_links( $links, $file ) {

    if ( $file == plugin_basename(__FILE__) ) {

        return array_merge($links, array( 
            '<a href="//wordpress.org/extend/plugins/ideas/">' . __('Rate Plugin', 'ideas') . '</a>',
            '<a href="//heyitsgeorge.com/support/">' . __('Get Support', 'ideas') . '</a>'
        ));
    }

    return $links;
}
add_filter( 'plugin_row_meta', 'ideas_meta_links', 10, 2 );


/**
 *  Get the ball rollin'
 *  @since 1.0
 */
function ideas_init() {

    // register post type
    ideas_register_post_type();

    // load meta boxes
    add_action( 'add_meta_boxes', 'ideas_add_meta_boxes' );

    // admin page
    if( is_admin() ) include_once('admin/ideas-admin.php');

}
add_action('init', 'ideas_init');





/**
 *  @todo integrate markdown syntax support in content box (for lists, embedded links, etc.)
 *  @todo better handling of links in links box + links column
 */
?>