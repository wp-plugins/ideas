<?php
/**
 *  Admin-only functions + CSS
 *  @package ideas
 */ 



/**
 *  Make columns for the idea post type edit page
 *  @since 1.0
 */
function ideas_edit_idea_columns( $columns ) {

    $columns = array(
        'title' => __('Title', 'ideas'),
        'idea_content' => __('Idea Summary', 'ideas'),
        'idea_links' => __('Idea Links', 'ideas'),
        'date' => __('Date', 'ideas')
    );

    return $columns;
}



/**
 *  Output idea content into relevant columns
 *  @since 1.0
 */
function ideas_manage_idea_columns( $column, $post_id ) {
    global $post;

    switch( $column ) {

        case 'idea_content' :

            $ideas_column_content = get_post_meta( $post_id, 'ideas_content_meta_box', true );
            echo nl2br($ideas_column_content);
        
            break;

        case 'idea_links' :
        
            $ideas_column_links = get_post_meta( $post_id, 'ideas_links_meta_box', true );
            echo nl2br($ideas_column_links);            
        
            break;

        default :
            
            break;
    }
}


/**
 *  Add the custom columns we made to idea post type edit page
 *  @since 1.0
 */
add_filter( 'manage_edit-idea_columns', 'ideas_edit_idea_columns' ) ;
add_action( 'manage_idea_posts_custom_column', 'ideas_manage_idea_columns', 10, 2 );



/**
 *  Enqueue functions.js
 *  @since 1.0
 */
function ideas_load_admin_scripts() {

    wp_enqueue_style('ideas-admin-styles', plugins_url('admin/ideas-admin.css', dirname(__FILE__)), '1.0');

}
add_action('admin_init','ideas_load_admin_scripts');

?>