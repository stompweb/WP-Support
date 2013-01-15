<?php

/* Register post type & taxonomies */
function wps_register_support() {

    // Register Post Type
    $labels = array(
        'name' => 'Tickets',
        'singular_name' => 'Ticket',
        'add_new' => 'Add New Ticket',
        'add_new_item' => 'Add New Ticket',
        'edit_item' => '',
        'new_item' => 'New Ticket',
        'all_items' => 'All Tickets',
        'view_item' => 'View Tickets',
        'search_items' => 'Search Tickets',
        'not_found' =>  'No tickets found',
        'not_found_in_trash' => 'No tickets found in Trash',
        'menu_name' => 'Support'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => false,
        'show_ui' => true, 
        'show_in_menu' => true, 
        'query_var' => false,
        'rewrite' => array( 'slug' => 'support' ),
        'capability_type' => 'post',
        'has_archive' => false, 
        'hierarchical' => false,
        'menu_position' => 100,
        'menu_icon' => WPS_PLUGIN_URL . '/assets/images/wps-support-icon.png',
        'supports' => array( 'title', 'editor', 'comments' )
    ); 

    register_post_type( 'wps_support', $args );

    // Register Taxonomy
    $labels = array(
        'name' => _x( 'Statuses', 'taxonomy general name' ),
        'singular_name' => _x( 'Status', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Statuses' ),
        'popular_items' => __( 'Popular Statuses' ),
        'all_items' => __( 'All Statuses' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Status' ), 
        'update_item' => __( 'Update Status' ),
        'add_new_item' => __( 'Add New Status' ),
        'new_item_name' => __( 'New Status Name' ),
        'menu_name' => __( 'Statuses' ),
    ); 

    register_taxonomy('wps_status','wps_support',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => false,
        'query_var' => true,
    ));  
}
add_action( 'init', 'wps_register_support' );

/* Remove comment box from the Add/Edit screen */
function wps_remove_comment_status() {
    remove_meta_box('commentstatusdiv', 'wps_support', 'normal');   
}
add_action( 'admin_menu', 'wps_remove_comment_status' );

/* Add custom columns to edit screen for post type */
function wps_edit_issue_column( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',           
        'wps_title' => __( 'Title' ),       
        'wps_comments' => __( 'Discussion' ),                
        'wps_status' => __( 'Status' ),        
        'author' => __( 'Raised by' ),        
        'date' => __( 'Date' )
    );

    return $columns;
}
add_filter( 'manage_edit-wps_support_columns', 'wps_edit_issue_column' ) ;

/* Define custom column content */
function wps_manage_wps_support_columns( $column, $post_id ) {
    global $post;

    switch( $column ) {
        
        case 'wps_title' :
            echo '#' . sprintf('%03d', get_the_id());        
            echo ' &nbsp;<a href="' . get_bloginfo("url") . '/wp-admin/edit.php?post_type=wps_support&page=edit-ticket&ticket=' . get_the_id() . '" class="wps_title_link">' . get_the_title() . '</a>'; 
            break;

        /* If displaying the 'duration' column. */
        case 'wps_comments' :

            comments_number();

            break;

        /* If displaying the 'genre' column. */
        case 'wps_status' :

            $status = wp_get_post_terms( get_the_ID(), 'wps_status' ); 
            echo '<div class="status ' . $status[0]->slug . '">' . $status[0]->name . '</div>';
            break;

        /* Just break out of the switch statement for everything else. */
        default :
            break;
    }
}
add_action( 'manage_wps_support_posts_custom_column', 'wps_manage_wps_support_columns', 10, 2 );

/* Add status dropdown to the edit screen for filtering */
function wps_add_status_filtering() {
    global $typenow;
    global $wp_query;
    if ($typenow=='wps_support') {
        $taxonomy = 'wps_status';
        $status_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
            'show_option_all' =>  __("Show All {$status_taxonomy->label}"),
            'taxonomy'        =>  $taxonomy,
            'name'            =>  'wps_status',
            'orderby'         =>  'name',
            'selected'        =>  $wp_query->query['term'],
            'hierarchical'    =>  true,
            'depth'           =>  3,
            'show_count'      =>  true, // Show # listings in parens
            'hide_empty'      =>  true, // Don't show businesses w/o listings
        ));
    }
}
add_action('restrict_manage_posts','wps_add_status_filtering');

/* If the status is sent in the query string then filter by it */
function wps_perform_status_filtering( $query ) {
    $qv = &$query->query_vars;
    if ( ( $qv['wps_status'] ) && is_numeric( $qv['wps_status'] ) ) {
        $term = get_term_by( 'id', $qv['wps_status'], 'wps_status' );
        $qv['wps_status'] = $term->slug;
    }
}
add_filter( 'parse_query','wps_perform_status_filtering' );

/* Allow ticket numbers to be searchable */
function wps_id_search( $wp ) {
    global $pagenow;

    if( 'edit.php' != $pagenow )
        return;

    if( !isset( $wp->query_vars['s'] ) )
        return;

    // If it's a search but there's no prefix, return
    if( '#' != substr( $wp->query_vars['s'], 0, 1 ) )
        return;

    // Validate the numeric value
    $id = absint( substr( $wp->query_vars['s'], 1 ) );
    if( !$id )
        return; // Return if no ID, absint returns 0 for invalid values

    // If we reach here, all criteria is fulfilled, unset search and select by ID instead
    unset( $wp->query_vars['s'] );
    $wp->query_vars['p'] = $id;
}
add_action( 'parse_request', 'wps_id_search' );

/* Set the status to 'Unresolved' when the user adds the ticket, don't mind if it autosaves! */
function wps_set_status($post_id) {

    if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $post_type = get_post_type($post_id);

    if ( 'wps_support' == $post_type ) {
        if( !has_term( '', 'wps_status',  $post_id) ) {
            wp_set_post_terms( $post_id, 'Unresolved', 'wps_status' );
        }
    }
    
}
add_action( 'save_post', 'wps_set_status');
?>