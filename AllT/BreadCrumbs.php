<?php
/**
 * This is for the displaying menu and siblings
 * 
 * 
 * @since 0.0.1
 * */
class AllT_BreadCrumbs{
	/**
	 * instance of this class
	 *
	 * @since 0.0.1
	 * @access protected
	 * @var	null
	 * */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	public function menu($theme_locations, $parent_home = '#', $site_home = null){
		global $posts;
		$breadcrumb_items = array();
		$posts_id = $posts[0]->ID;
		$posts_title = $posts[0]->post_title;
		//switch_to_blog( $origin_id );
		//wp_nav_menu( $args );   
		$locations = get_nav_menu_locations();
		$menu = get_term( $locations[$theme_locations], 'nav_menu' );
		//_dump($menu);
		//_dump($locations,1);
		$allteam_nav_menu = new AllT_NavMenuChildren;
		$wp_menus = wp_get_nav_menu_items($menu->term_id);
		//_dump($wp_menus);
		$menu_id = $allteam_nav_menu->allteam_search_posts_inmenu($posts_id, $wp_menus);
		//_dump($menu_id);
		$parse_menus = $allteam_nav_menu->allteam_parse_menu($menu->name);
		//_dump($parse_menus);
		if( !empty($parse_menus->$menu_id) ){
			$parent_arr = array();
			if( !empty($wp_menus)){
				foreach($wp_menus as $k => $v){
					if( $v->ID == $menu_id ){
						$parent_arr = $v;
					}
				}
			}
			//_dump($parent_arr);
			//_dump($breadcrumb_items);
			echo '<ol class="breadcrumb allteam-helper-breadcrumb">';
				echo '<li class="breadcrumb-item">';
					echo '<a href="'.$parent_home.'">';
					echo 'Home';
					echo '</a>';
				echo '</li>';
				echo '<li class="breadcrumb-item">';
					echo '<a href="'.get_bloginfo('url').'">';
					echo get_bloginfo('name');
					echo '</a>';
				echo '</li>';
				if( !empty($parent_arr) && $posts_id != $parent_arr->object_id ){
					echo '<li class="breadcrumb-item breadcrumb-post-id-'.$parent_arr->object_id.'">';
					echo '<a href="'.get_permalink($parent_arr->object_id).'">';
						echo $parent_arr->title;
					echo '</a>';
					echo '</li>';
				}
			if( !empty($parse_menus->$menu_id) ){
				$stop = false;
				foreach($parse_menus->$menu_id as $k => $v){
					$active = ($posts_id == $v->id) ? 'active':'';
					if( $active == 'active' ){
						echo '<li class="breadcrumb-item breadcrumb-post-id-'.$v->id.' '.$active.'">';
						echo $v->title;
						$stop = true;
						echo '</li>';
					}
				}
			
			}
			echo '</ol>';
		}
		//restore_current_blog();
	}
	
	public function display() {
       
		// Settings
		$separator          = '&gt;';
		$breadcrums_id      = 'breadcrumb';
		$breadcrums_class   = 'breadcrumb';
		$home_title         = 'Homepage';
		  
		// If you have any custom post types with custom taxonomies, put the taxonomy name below (e.g. product_cat)
		$custom_taxonomy    = 'product_cat';
		   
		// Get the query & post information
		global $post,$wp_query;
		   
		// Do not display on the homepage
		if ( !is_front_page() ) {
		   
			// Build the breadcrums
			echo '<ul id="' . $breadcrums_id . '" class="' . $breadcrums_class . '">';
			   
			// Home page
			echo '<li class="item-home"><a class="bread-link bread-home" href="' . get_home_url() . '" title="' . $home_title . '">' . $home_title . '</a></li>';
			echo '<li class="separator separator-home"> ' . $separator . ' </li>';
			   
			if ( is_archive() && !is_tax() && !is_category() && !is_tag() ) {
				  
				echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . post_type_archive_title($prefix, false) . '</strong></li>';
				  
			} else if ( is_archive() && is_tax() && !is_category() && !is_tag() ) {
				  
				// If post is a custom post type
				$post_type = get_post_type();
				  
				// If it is a custom post type display name and link
				if($post_type != 'post') {
					  
					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);
				  
					echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
					echo '<li class="separator"> ' . $separator . ' </li>';
				  
				}
				  
				$custom_tax_name = get_queried_object()->name;
				echo '<li class="item-current item-archive"><strong class="bread-current bread-archive">' . $custom_tax_name . '</strong></li>';
				  
			} else if ( is_single() ) {
				  
				// If post is a custom post type
				$post_type = get_post_type();
				  
				// If it is a custom post type display name and link
				if($post_type != 'post') {
					  
					$post_type_object = get_post_type_object($post_type);
					$post_type_archive = get_post_type_archive_link($post_type);
				  
					echo '<li class="item-cat item-custom-post-type-' . $post_type . '"><a class="bread-cat bread-custom-post-type-' . $post_type . '" href="' . $post_type_archive . '" title="' . $post_type_object->labels->name . '">' . $post_type_object->labels->name . '</a></li>';
					echo '<li class="separator"> ' . $separator . ' </li>';
				  
				}
				  
				// Get post category info
				$category = get_the_category();
				 
				if(!empty($category)) {
				  
					// Get last category post is in
					$last_category = end(array_values($category));
					  
					// Get parent any categories and create array
					$get_cat_parents = rtrim(get_category_parents($last_category->term_id, true, ','),',');
					$cat_parents = explode(',',$get_cat_parents);
					  
					// Loop through parent categories and store in variable $cat_display
					$cat_display = '';
					foreach($cat_parents as $parents) {
						$cat_display .= '<li class="item-cat">'.$parents.'</li>';
						$cat_display .= '<li class="separator"> ' . $separator . ' </li>';
					}
				 
				}
				  
				// If it's a custom post type within a custom taxonomy
				$taxonomy_exists = taxonomy_exists($custom_taxonomy);
				if(empty($last_category) && !empty($custom_taxonomy) && $taxonomy_exists) {
					   
					$taxonomy_terms = get_the_terms( $post->ID, $custom_taxonomy );
					$cat_id         = $taxonomy_terms[0]->term_id;
					$cat_nicename   = $taxonomy_terms[0]->slug;
					$cat_link       = get_term_link($taxonomy_terms[0]->term_id, $custom_taxonomy);
					$cat_name       = $taxonomy_terms[0]->name;
				   
				}
				  
				// Check if the post is in a category
				if(!empty($last_category)) {
					echo $cat_display;
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
					  
				// Else if post is in a custom taxonomy
				} else if(!empty($cat_id)) {
					  
					echo '<li class="item-cat item-cat-' . $cat_id . ' item-cat-' . $cat_nicename . '"><a class="bread-cat bread-cat-' . $cat_id . ' bread-cat-' . $cat_nicename . '" href="' . $cat_link . '" title="' . $cat_name . '">' . $cat_name . '</a></li>';
					echo '<li class="separator"> ' . $separator . ' </li>';
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
				  
				} else {
					  
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '" title="' . get_the_title() . '">' . get_the_title() . '</strong></li>';
					  
				}
				  
			} else if ( is_category() ) {
				   
				// Category page
				echo '<li class="item-current item-cat"><strong class="bread-current bread-cat">' . single_cat_title('', false) . '</strong></li>';
				   
			} else if ( is_page() ) {
				   
				// Standard page
				if( $post->post_parent ){
					   
					// If child page, get parents 
					$anc = get_post_ancestors( $post->ID );
					   
					// Get parents in the right order
					$anc = array_reverse($anc);
					   
					// Parent page loop
					if ( !isset( $parents ) ) $parents = null;
					foreach ( $anc as $ancestor ) {
						$parents .= '<li class="item-parent item-parent-' . $ancestor . '"><a class="bread-parent bread-parent-' . $ancestor . '" href="' . get_permalink($ancestor) . '" title="' . get_the_title($ancestor) . '">' . get_the_title($ancestor) . '</a></li>';
						$parents .= '<li class="separator separator-' . $ancestor . '"> ' . $separator . ' </li>';
					}
					   
					// Display parent pages
					echo $parents;
					   
					// Current page
					echo '<li class="item-current item-' . $post->ID . '"><strong title="' . get_the_title() . '"> ' . get_the_title() . '</strong></li>';
					   
				} else {
					   
					// Just display current page if not parents
					echo '<li class="item-current item-' . $post->ID . '"><strong class="bread-current bread-' . $post->ID . '"> ' . get_the_title() . '</strong></li>';
					   
				}
				   
			} else if ( is_tag() ) {
				   
				// Tag page
				   
				// Get tag information
				$term_id        = get_query_var('tag_id');
				$taxonomy       = 'post_tag';
				$args           = 'include=' . $term_id;
				$terms          = get_terms( $taxonomy, $args );
				$get_term_id    = $terms[0]->term_id;
				$get_term_slug  = $terms[0]->slug;
				$get_term_name  = $terms[0]->name;
				   
				// Display the tag name
				echo '<li class="item-current item-tag-' . $get_term_id . ' item-tag-' . $get_term_slug . '"><strong class="bread-current bread-tag-' . $get_term_id . ' bread-tag-' . $get_term_slug . '">' . $get_term_name . '</strong></li>';
			   
			} elseif ( is_day() ) {
				   
				// Day archive
				   
				// Year link
				echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
				   
				// Month link
				echo '<li class="item-month item-month-' . get_the_time('m') . '"><a class="bread-month bread-month-' . get_the_time('m') . '" href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('m') . '"> ' . $separator . ' </li>';
				   
				// Day display
				echo '<li class="item-current item-' . get_the_time('j') . '"><strong class="bread-current bread-' . get_the_time('j') . '"> ' . get_the_time('jS') . ' ' . get_the_time('M') . ' Archives</strong></li>';
				   
			} else if ( is_month() ) {
				   
				// Month Archive
				   
				// Year link
				echo '<li class="item-year item-year-' . get_the_time('Y') . '"><a class="bread-year bread-year-' . get_the_time('Y') . '" href="' . get_year_link( get_the_time('Y') ) . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</a></li>';
				echo '<li class="separator separator-' . get_the_time('Y') . '"> ' . $separator . ' </li>';
				   
				// Month display
				echo '<li class="item-month item-month-' . get_the_time('m') . '"><strong class="bread-month bread-month-' . get_the_time('m') . '" title="' . get_the_time('M') . '">' . get_the_time('M') . ' Archives</strong></li>';
				   
			} else if ( is_year() ) {
				   
				// Display year archive
				echo '<li class="item-current item-current-' . get_the_time('Y') . '"><strong class="bread-current bread-current-' . get_the_time('Y') . '" title="' . get_the_time('Y') . '">' . get_the_time('Y') . ' Archives</strong></li>';
				   
			} else if ( is_author() ) {
				   
				// Auhor archive
				   
				// Get the author information
				global $author;
				$userdata = get_userdata( $author );
				   
				// Display author name
				echo '<li class="item-current item-current-' . $userdata->user_nicename . '"><strong class="bread-current bread-current-' . $userdata->user_nicename . '" title="' . $userdata->display_name . '">' . 'Author: ' . $userdata->display_name . '</strong></li>';
			   
			} else if ( get_query_var('paged') ) {
				   
				// Paginated archives
				echo '<li class="item-current item-current-' . get_query_var('paged') . '"><strong class="bread-current bread-current-' . get_query_var('paged') . '" title="Page ' . get_query_var('paged') . '">'.__('Page') . ' ' . get_query_var('paged') . '</strong></li>';
				   
			} else if ( is_search() ) {
			   
				// Search results page
				echo '<li class="item-current item-current-' . get_search_query() . '"><strong class="bread-current bread-current-' . get_search_query() . '" title="Search results for: ' . get_search_query() . '">Search results for: ' . get_search_query() . '</strong></li>';
			   
			} elseif ( is_404() ) {
				   
				// 404 page
				echo '<li>' . 'Error 404' . '</li>';
			}
		   
			echo '</ul>';
			   
		}
		   
	}

	
	public function __construct(){}
}

