<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Truelysell_Core_Search class.
 */
class Truelysell_Core_Search {


	public $found_posts = 0;
	/**
	 * Constructor
	 */
	public function __construct() {


		add_action( 'pre_get_posts', array( $this, 'pre_get_posts_listings' ), 0 );
		add_action( 'pre_get_posts', array( $this, 'remove_products_from_search' ), 0 );
		// add_filter( 'posts_orderby', array( $this, 'featured_filter' ), 10, 2 );
		// add_filter( 'posts_request', array( $this, 'featured_filter' ), 10, 2 );


		add_filter( 'posts_results', array( $this,'open_now_results_filter' ));
		add_filter( 'found_posts', array( $this,'open_now_results_filter_pagination'), 1, 2 );

		//add_action( 'parse_tax_query', array( $this, 'parse_tax_query_listings' ), 1 );
		add_shortcode( 'truelysell_search_form', array($this, 'output_search_form'));
		
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

		add_action( 'parse_query', [ $this, 'admin_search_by_category' ] );
		add_action('restrict_manage_posts',  [ $this, 'admin_filter_search_by_category']);
		
		if(get_option('truelysell_search_name_autocomplete')) {
			add_action( 'wp_print_footer_scripts', array( __CLASS__, 'wp_print_footer_scripts' ), 11 );
	        add_action( 'wp_ajax_truelysell_core_incremental_listing_suggest', array( __CLASS__, 'wp_ajax_truelysell_core_incremental_listing_suggest' ) );
	        add_action( 'wp_ajax_nopriv_truelysell_core_incremental_listing_suggest', array( __CLASS__, 'wp_ajax_truelysell_core_incremental_listing_suggest' ) );
	    }

	    add_action( 'wp_ajax_nopriv_truelysell_get_listings', array( $this, 'ajax_get_listings' ) );
		add_action( 'wp_ajax_truelysell_get_listings', array( $this, 'ajax_get_listings' ) );

		add_action( 'wp_ajax_nopriv_truelysell_get_features_from_category', array( $this, 'ajax_get_features_from_category' ) );
		add_action( 'wp_ajax_truelysell_get_features_from_category', array( $this, 'ajax_get_features_from_category' ) );
		
		add_action( 'wp_ajax_nopriv_truelysell_get_features_ids_from_category', array( $this, 'ajax_get_features_ids_from_category' ) );
		add_action( 'wp_ajax_truelysell_get_features_ids_from_category', array( $this, 'ajax_get_features_ids_from_category' ) );
   				
   		add_action( 'wp_ajax_nopriv_truelysell_get_listing_types_from_categories', array( $this, 'ajax_get_listing_types_from_categories' ) );
		add_action( 'wp_ajax_truelysell_get_listing_types_from_categories', array( $this, 'ajax_get_listing_types_from_categories' ) );
   		
 		add_filter( 'posts_where', array( $this,'truelysell_date_range_filter') );

	}

function admin_filter_search_by_category() {
	global $typenow;
	$post_type = 'listing'; // change to your post type
	$taxonomy  = 'listing_category'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => sprintf( __( 'Show all %s', 'truelysell_core' ), $info_taxonomy->label ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}
	
function admin_search_by_category($query) {
	global $pagenow;
	$post_type = 'listing'; // change to your post type
	$taxonomy  = 'listing_category'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}


function truelysell_date_range_filter( $where ) {
	
	global $wpdb;
	global $wp_query;
	if (!isset($wp_query) || !method_exists($wp_query, 'get'))
	return;

	$date_range = get_query_var( 'date_range' );
	
	if(!empty($date_range)) : 
//TODO replace / with - if first is day - month- year
		$dates = explode(' - ',$date_range);
		//setcookie('truelysell_date_range', $date_range, time()+31556926);
		$date_start = $dates[0];
		$date_end = $dates[1];
		
		$date_start_object = DateTime::createFromFormat('!'.truelysell_date_time_wp_format_php(), $date_start);
		$date_end_object = DateTime::createFromFormat('!'.truelysell_date_time_wp_format_php(), $date_end);
		
		$format_date_start 	= esc_sql($date_start_object->format("Y-m-d H:i:s"));
		$format_date_end 	= esc_sql($date_end_object->format("Y-m-d H:i:s"));

		// $format_date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_start ) ) ) );
		// $format_date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $wpdb->esc_like( $date_end ) ) ) );
		// //    
		// $booking_hours = Truelysell_Core_Bookings_Calendar::wpk_change_booking_hours( $date_start, $date_end );
  //       $date_start = $booking_hours[ 'date_start' ];
  //       $date_end = $booking_hours[ 'date_end' ];

	 	$table_name = $wpdb->prefix . 'bookings_calendar';
	 		
	    $where .= $GLOBALS['wpdb']->prepare(  " AND {$wpdb->prefix}posts.ID ".
	        'NOT IN ( '.
	            'SELECT listing_id '.
	            "FROM {$wpdb->prefix}bookings_calendar ".
	            'WHERE 
            	(( %s > date_start AND %s < date_end ) 
            	OR 
            	( %s > date_start AND %s < date_end ) 
            	OR 
            	( date_start >= %s AND date_end < %s ))
            	AND type = "reservation" AND NOT status="cancelled" AND NOT status="expired"
            	GROUP BY listing_id '.
	        ' ) ', $format_date_start, $format_date_start, $format_date_end,  $format_date_end, $format_date_start, $format_date_end );
	  
	   //var_dump($where);
	endif;
    
    return $where;
}

	public function remove_products_from_search($query){

	    /* check is front end main loop content */
	    if(is_admin() || !$query->is_main_query()) return;

	    /* check is search result query */
	    if($query->is_search()){
	    	if(isset($_GET['post_type']) && $_GET['post_type'] == 'product'){

	    	} else {
		  			$post_type_to_remove = 'product';
			        /* get all searchable post types */
			        $searchable_post_types = get_post_types(array('exclude_from_search' => false));

			        /* make sure you got the proper results, and that your post type is in the results */
			        if(is_array($searchable_post_types) && in_array($post_type_to_remove, $searchable_post_types)){
			            /* remove the post type from the array */
			            unset( $searchable_post_types[ $post_type_to_remove ] );
			            /* set the query to the remaining searchable post types */
			            $query->set('post_type', $searchable_post_types);
			        }
	    	}
	      
	    }
	}


	public function open_now_results_filter( $posts ) {

		if(isset($_GET['open_now'])) {
			$filtered_posts = array();
			
			foreach ( $posts as $post ) {
				if( truelysell_check_if_open($post) ){ 
					$filtered_posts[] = $post;
				}
				
			}
			$this->found_posts = count($filtered_posts);;
			return $filtered_posts ;
		}

		return $posts;
		
	}

	function open_now_results_filter_pagination( $found_posts, $query ) {
		if(isset($_GET['open_now'])) {
			// Define the homepage offset...
			$found_posts = $this->found_posts;
		} 
		return $found_posts;
	}


	static function wp_print_footer_scripts() {  
		?>
	    <script type="text/javascript">
	        (function($){
	        $(document).ready(function(){

	            $( '#keyword_search.title-autocomplete' ).autocomplete({
	                
	                source: function(req, response){
	                    $.getJSON('<?php echo admin_url( 'admin-ajax.php' ); ?>'+'?callback=?&action=truelysell_core_incremental_listing_suggest', req, response);
	                },
	                select: function(event, ui) {
	                    window.location.href=ui.item.link;
	                },
	                minLength: 3,
	            }); 
	         });

	        })(this.jQuery);

	           
	    </script><?php
    }

    static function wp_ajax_truelysell_core_incremental_listing_suggest() {
    
        $suggestions = array();
        $posts = get_posts( array(
            's' => $_REQUEST['term'],
            'post_type'     => 'listing',
        ) );
        global $post;
        $results = array();
        foreach ($posts as $post) {
            setup_postdata($post);
            $suggestion = array();
            $suggestion['label'] =  html_entity_decode($post->post_title, ENT_QUOTES, 'UTF-8');
            $suggestion['link'] = get_permalink($post->ID);
            
            $suggestions[] = $suggestion;
        }
        // JSON encode and echo
            $response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
            echo $response;
             // Don't forget to exit!
            exit;

    }

	public function add_query_vars($vars) {
		
		$new_vars = $this->build_available_query_vars();
	    $vars = array_merge( $new_vars, $vars );
		return $vars;

	}

	public static function build_available_query_vars(){
		$query_vars = array();
		$taxonomy_objects = get_object_taxonomies( 'listing', 'objects' );
        foreach ($taxonomy_objects as $tax) {
        	array_push($query_vars, 'tax-'.$tax->name);
        }
      
        $location = Truelysell_Core_Meta_Boxes::meta_boxes_location();
        
            foreach ($location['fields'] as $key => $field) {
              	array_push($query_vars, $field['id']);
              	
        }
       $event = Truelysell_Core_Meta_Boxes::meta_boxes_event();
            foreach ($event['fields']  as $key => $field) {
              	array_push($query_vars, $field['id']);
              	
        }
        $prices = Truelysell_Core_Meta_Boxes::meta_boxes_prices();
            foreach ($prices['fields']  as $key => $field) {
              	array_push($query_vars, $field['id']);
              	
        }  
        $contact = Truelysell_Core_Meta_Boxes::meta_boxes_contact();
        
            foreach ($contact['fields']  as $key => $field) {
              	array_push($query_vars, $field['id']);
              	
        } 
        $rental = Truelysell_Core_Meta_Boxes::meta_boxes_rental();
            foreach ($rental['fields']  as $key => $field) {
              	array_push($query_vars, $field['id']);
              	
        }  
        $custom = Truelysell_Core_Meta_Boxes::meta_boxes_custom();
            foreach ($custom['fields']  as $key => $field) {
              	array_push($query_vars, $field['id']);
              	
        } 
        array_push($query_vars, '_price_range');
        array_push($query_vars, '_listing_type');
        //array_push($query_vars, '_verified');
        array_push($query_vars, '_price');
        array_push($query_vars, '_max_guests');
		return $query_vars;
	}

	public function pre_get_posts_listings( $query ) {

		if ( is_admin() || ! $query->is_main_query() ){
			return $query;


		}
		if ( !is_admin() && $query->is_main_query() && is_post_type_archive( 'listing' ) ) {
			$per_page = get_option('truelysell_listings_per_page',10);
		    $query->set( 'posts_per_page', $per_page );
		    $query->set( 'post_type', 'listing' );
		    $query->set( 'post_status', 'publish' );
		}

		if ( is_tax('listing_category') || is_tax('service_category') || is_tax('event_category') || is_tax('rental_category') || is_tax('listing_feature')  || is_tax('region') ) {

			$per_page = get_option('truelysell_listings_per_page',10);
		    $query->set( 'posts_per_page', $per_page );	
		}

		if ( is_post_type_archive( 'listing' ) || is_author() || is_tax('listing_category') || is_tax('listing_feature') || is_tax('event_category') || is_tax('service_category') || is_tax('rental_category') || is_tax('region')) {
			
			$ordering_args = Truelysell_Core_Listing::get_listings_ordering_args( );
			
			if(isset($ordering_args['meta_key']) && $ordering_args['meta_key'] != '_featured' ){
				$query->set('meta_key', $ordering_args['meta_key']);
			} 

			$query->set('orderby', $ordering_args['orderby']);
        	$query->set('order', $ordering_args['order'] );

			$keyword = get_query_var( 'keyword_search' );
			
			$date_range =  (isset($_REQUEST['date_range'])) ? sanitize_text_field(  $_REQUEST['date_range']  ) : '';
				
			$keyword_search = get_option('truelysell_keyword_search', 'search_title');
			$search_mode = get_option('truelysell_search_mode', 'exact');

			$keywords_post_ids = array();
			$location_post_ids = array();
	        if ( $keyword  ) {
				global $wpdb;
				// Trim and explode keywords
				if($search_mode == 'exact'){
					$keywords = array_map('trim', explode('+', $keyword));
				} else {
					$keywords = array_map('trim', explode(' ', $keyword));
				}
				
			
				// Setup SQL
				$posts_keywords_sql    = array();
				$postmeta_keywords_sql = array();
				// Loop through keywords and create SQL snippets
				foreach ($keywords as $keyword) {
					# code...
					if (strlen($keyword)>2){


					// Create post meta SQL
					if($keyword_search == 'search_title'){
							$postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql($keyword) . "%' AND meta_key IN ('truelysell_subtitle','listing_title','listing_description','keywords') ";
					} else {
							$postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql($keyword) . "%'";
					}
					
					// Create post title and content SQL
					$posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql( $keyword ) . "%' OR post_content LIKE '%" . esc_sql(  $keyword ) . "%' ";
					}
				}

				if(!empty($postmeta_keywords_sql)){
					// Get post IDs from post meta search
					
					$post_ids = $wpdb->get_col( "
					    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
					    WHERE " . implode( ' OR ', $postmeta_keywords_sql ) . "
					" );
				
				} else {
					$post_ids = array();
				}
				
					
				// Merge with post IDs from post title and content search
				
					$keywords_post_ids = array_merge( $post_ids, $wpdb->get_col( "
					    SELECT ID FROM {$wpdb->posts}
					    WHERE ( " . implode( ' OR ', $posts_keywords_sql ) . " )
					    AND post_type = 'listing'
					   
					" ), array( 0 ) );
				

			}
			$location = get_query_var( 'location_search' );
			
			if( $location ) {

				$radius = get_query_var('search_radius');
	        	if(empty($radius) && get_option('truelysell_radius_state') == 'enabled') {
	        		$radius = get_option('truelysell_maps_default_radius');
	        	}
				$radius_type = get_option('truelysell_radius_unit','km');
				$geocoding_provider = get_option('truelysell_geocoding_provider','google');
				if($geocoding_provider == 'google'){
					$radius_api_key = get_option( 'truelysell_maps_api_server' );	
				} else {
					$radius_api_key = get_option( 'truelysell_geoapify_maps_api_server' );	
				}
				
				if(!empty($location) && !empty($radius) && !empty($radius_api_key)) {

					//search by google
				
					$latlng = truelysell_core_geocode($location);
					
					$nearbyposts = truelysell_core_get_nearby_listings($latlng[0], $latlng[1], $radius, $radius_type ); 

					truelysell_core_array_sort_by_column($nearbyposts,'distance');
					$location_post_ids = array_unique(array_column($nearbyposts, 'post_id'));

					if(empty($location_post_ids)) {
						$location_post_ids = array(0);
					}

				} else {

					//search by text
					global $wpdb;
					// Trim and explode keywords
					$locations = array_map( 'trim', explode( ',', $location  ) );
				
					// Setup SQL
					$posts_locations_sql    = array();
					$postmeta_locations_sql = array();
					// Loop through keywords and create SQL snippets
					
					if(get_option('truelysell_search_only_address','off') == 'on') {
						$postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql( $locations[0] ) . "%'  AND meta_key = '_address'";
						$postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql( $locations[0] ) . "%'  AND meta_key = '_friendly_address'" ;
					} else {
						// Create post meta SQL
						$postmeta_locations_sql[] = " meta_value LIKE '%" . esc_sql( $locations[0] ) . "%' ";
						// Create post title and content SQL
						$posts_locations_sql[]    = " post_title LIKE '%" . esc_sql( $locations[0] ) . "%' OR post_content LIKE '%" . esc_sql(  $locations[0] ) . "%' ";
					}

					// Get post IDs from post meta search
					$post_ids = $wpdb->get_col( "
					    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
					    WHERE " . implode( ' OR ', $postmeta_locations_sql ) . "

					" );
		
					// Merge with post IDs from post title and content search
					if(get_option('truelysell_search_only_address','off') == 'on') {
						$location_post_ids = array_merge( $post_ids,array( 0 ) );
					} else {
						$location_post_ids = array_merge( $post_ids, $wpdb->get_col( "
						    SELECT ID FROM {$wpdb->posts}
						    WHERE ( " . implode( ' OR ', $posts_locations_sql ) . " )
						    AND post_type = 'listing'
					    	AND post_status = 'publish'
						   
						" ), array( 0 ) );
					}


				}
			}
	
			if ( sizeof( $keywords_post_ids ) != 0 && sizeof( $location_post_ids ) != 0 ) {
				$post_ids = array_intersect($keywords_post_ids, $location_post_ids);
				$query->set( 'post__in', $post_ids );
			} else if (sizeof( $keywords_post_ids ) != 0 && sizeof( $location_post_ids ) == 0) {
				$query->set( 'post__in', $keywords_post_ids );
			} else if (sizeof( $keywords_post_ids ) == 0 && sizeof( $location_post_ids ) != 0) {

				$query->set( 'post__in', $location_post_ids );
			}
			

			// if ( ! empty( $post_ids ) ) {
		 //        $query->set( 'post__in', $post_ids );
		 //    }

			$query->set('post_type', 'listing');
	 		$args = array();
			$tax_query = array();
			
			$tax_query = array(
		        'relation' => 'AND',
		    );
			$taxonomy_objects = get_object_taxonomies( 'listing', 'objects' );
			
            foreach ($taxonomy_objects as $tax) {
            	$get_tax = get_query_var( 'tax-'.$tax->name  );
            	if(is_array($get_tax)){
            		$tax_query[$tax->name] = array('relation'=> get_option('truelysell_taxonomy_or_and','OR'));

            		foreach ($get_tax as $key => $value) {
			    		array_push($tax_query[$tax->name], array(
				           'taxonomy' =>   $tax->name,
				           'field'    =>   'slug',
				           'terms'    =>   $value,
				           
				        ));
				        
			    	}
			    	
            	} else {

	            	if( $get_tax ){

				    	$term = get_term_by('slug', $get_tax, $tax->name);
				    	if($term){
					    	array_push($tax_query, array(
					           'taxonomy' =>  $tax->name,
					           'field'    =>  'slug',
					           'terms'    =>  $term->slug,
					           'operator' =>  'IN'
					        ));
						}
				    }
			 	}
            }
            

			$query->set('tax_query', $tax_query);	

			$available_query_vars = $this->build_available_query_vars();

			$meta_queries = array();

			foreach ($available_query_vars as $key => $meta_key) {
				
				if( substr($meta_key,0, 4) == "tax-") {
					continue;
				}
				if( $meta_key == '_price_range'){
					continue;
				}

				
				

					if(!empty($meta_min) && !empty($meta_max) ) {
				
						$meta_queries[] = array(
				            'key' =>  substr($meta_key,0, -4),
				            'value' => array($meta_min, $meta_max),
				            'compare' => 'BETWEEN',
				            'type' => 'NUMERIC'
				        );
				        $meta_max = false;
				        $meta_min = false;

					} else if(!empty($meta_min) && empty($meta_max) ) {
						$meta_queries[] = array(
				            'key' =>  substr($meta_key,0, -4),
				            'value' => $meta_min,
				            'compare' => '>=',
				            'type' => 'NUMERIC'
				        );
				        $meta_max = false;
				        $meta_min = false;
					} else if(empty($meta_min) && !empty($meta_max) ) {
						$meta_queries[] = array(
				            'key' =>  substr($meta_key,0, -4),
				            'value' => $meta_max,
				            'compare' => '<=',
				            'type' => 'NUMERIC'
				        );
				        $meta_max = false;
				        $meta_min = false;
					}

					if($meta_key == '_price'){
						$meta = get_query_var( '_price_range' );
						if(!empty($meta) && $meta != -1){
							
							$range = array_map( 'absint', explode( ',', $meta ) );

							$meta_queries[] = array(
							 	'relation' => 'OR',
							        array(
							            'relation' => 'OR',
							            array(
			                                'key' => '_price_min',
			                                'value' => $range,
			                                'compare' => 'BETWEEN',
			                                'type' => 'NUMERIC',
			                            ),
			                            array(
			                                'key' => '_price_max',
			                                'value' => $range,
			                                'compare' => 'BETWEEN',
			                                'type' => 'NUMERIC',
			                            ),
							 
							        ),
							        array(
							            'relation' => 'AND',
							            array(
			                                'key' => '_price_min',
			                                'value' => $range[0],
			                                'compare' => '<=',
			                                'type' => 'NUMERIC',
			                            ),
			                            array(
			                                'key' => '_price_max',
			                                'value' => $range[1],
			                                'compare' => '>=',
			                                'type' => 'NUMERIC',
			                            ),
							 
							        ),
					        );
					       
				        }
					} else {
						if (substr($meta_key, -4) == "_min" || substr($meta_key, -4) == "_max") { continue; }
						
						if( $meta_key == '_max_guests'){
							$meta = get_query_var( $meta_key );
						
								if ( $meta && $meta != -1) {
									
									$meta_queries[] = array(
							            'key' =>  '_max_guests',
							            'value' => $meta,
							             'compare' => '>=',
							            'type' => 'NUMERIC'
							        );
								}

						} else {
							
								$meta = get_query_var( $meta_key );
						
								if ( $meta && $meta != -1) {
									if(is_array($meta)){
									    $meta_queries[] = array(
    						                'key'     => $meta_key,
    						                'value'   => array_keys($meta), 
    						            );
									} else {
    									$meta_queries[] = array(
    						                'key'     => $meta_key,
    						                'value'   => $meta, 
    						            );
									}
								}
						}
						
					}

				

				

			}
			$listing_type = get_query_var( '_listing_type' );
			if($date_range && $listing_type == 'event'){
//check to apply only for events
					$dates = explode(' - ',$date_range);	
					//var_dump($dates);
					$date_start_obj = DateTime::createFromFormat(truelysell_date_time_wp_format_php(). ' H:i:s', $dates[0].' 00:00:00');
					
					if($date_start_obj){
						$date_start = $date_start_obj->getTimestamp();
					} else {
						$date_start = false;
					}
					
					$date_end_obj = DateTime::createFromFormat(truelysell_date_time_wp_format_php(). ' H:i:s', $dates[1].' 23:59:59');
					
					if($date_end_obj){
						$date_end = $date_end_obj->getTimestamp();
					} else {
						$date_end = false;
					}

					if($date_start && $date_end) {

						$meta_queries[] = array(
				            'relation' => 'OR',
				            array(
                                'key' => '_event_date_timestamp',
					            'value' => array($date_start, $date_end),
					            'compare' => 'BETWEEN',
					            'type' => 'NUMERIC'
                            ),
                            array(
                                'key' => '_event_date_end_timestamp',
					            'value' => array($date_start, $date_end),
					            'compare' => 'BETWEEN',
					            'type' => 'NUMERIC'
                            ),
				 
				        );

					}
							
				


				    
			}


			// var_dump($meta_queries);
			if( isset($ordering_args['meta_key']) && $ordering_args['meta_key'] == '_featured' ){

			
				$query->set('order', 'ASC DESC');
				$query->set('orderby', 'meta_value date');
				$query->set('meta_key', '_featured');
					
			}
		
			if(!empty($meta_queries)){
				$query->set('meta_query', array(
		            'relation' => 'AND',
		            $meta_queries 
		        ) );	

	        }
	    } 
// 	ini_set('xdebug.var_display_max_depth', '10');
					//  ini_set('xdebug.var_display_max_children', '256');
					//  ini_set('xdebug.var_display_max_data', '1024');
						
	    return $query;
	} /*eof function*/


public function ajax_get_listings() {


		global $wp_post_types;

		$template_loader = new Truelysell_Core_Template_Loader;

		$location  	= (isset($_REQUEST['location_search'])) ? sanitize_text_field( stripslashes( $_REQUEST['location_search'] ) ) : '';
		$keyword   	= (isset($_REQUEST['keyword_search'])) ? sanitize_text_field( stripslashes( $_REQUEST['keyword_search'] ) ) : '';
		$radius   	= (isset($_REQUEST['search_radius'])) ?  sanitize_text_field( stripslashes( $_REQUEST['search_radius'] ) ) : '';


		$orderby   	= (isset($_REQUEST['orderby'])) ?  sanitize_text_field( stripslashes( $_REQUEST['orderby'] ) ) : '';
		$order   	= (isset($_REQUEST['order'])) ?  sanitize_text_field( stripslashes( $_REQUEST['order'] ) ) : '';
		
		$style   	= sanitize_text_field( stripslashes( $_REQUEST['style'] ) );
		$grid_columns  = sanitize_text_field( stripslashes( $_REQUEST['grid_columns'] ) );
		$per_page   = sanitize_text_field( stripslashes( $_REQUEST['per_page'] ) );
		$date_range =  (isset($_REQUEST['date_range'])) ? sanitize_text_field(  $_REQUEST['date_range']  ) : '';
				

		$region   	= (isset($_REQUEST['tax-region'])) ?  sanitize_text_field(  $_REQUEST['tax-region']  ) : '';
		$category   	= (isset($_REQUEST['tax-listing_category'])) ?  sanitize_text_field(  $_REQUEST['tax-listing_category']  ) : '';
		$feature   	= (isset($_REQUEST['tax-listing_feature'])) ?  sanitize_text_field(  $_REQUEST['tax-listing_feature']  ) : '';

		$date_start = '';
		$date_end = '';
		
		if($date_range){

			$dates = explode(' - ',$date_range);	
			$date_start = $dates[0];
			$date_end = $dates[1];

			// $date_start = esc_sql ( date( "Y-m-d H:i:s", strtotime(  $date_start )  ) );
		 //    $date_end = esc_sql ( date( "Y-m-d H:i:s", strtotime( $date_end ) )  );
		    
		}
		
		if(empty($per_page)) { $per_page = get_option('truelysell_listings_per_page',10); }

		$query_args = array(
			'ignore_sticky_posts'    => 1,
			'post_type'         => 'listing',
			'orderby'           => $orderby,
			'order'             =>  $order,
			'offset'            => ( absint( $_REQUEST['page'] ) - 1 ) * absint( $per_page ),
			'location'   		=> $location,
			'keyword'   		=> $keyword,
			'search_radius'   	=> $radius,
			'posts_per_page'    => $per_page,
			'date_start'    	=> $date_start,
			'date_end'    		=> $date_end,
			'tax-region'    		=> $region,
			'tax-listing_feature'   => $feature,
			'tax-listing_category'  => $category,

		);
		
		$query_args['truelysell_orderby'] = (isset($_REQUEST['truelysell_core_order'])) ? sanitize_text_field( $_REQUEST['truelysell_core_order'] ) : false;

		$taxonomy_objects = get_object_taxonomies( 'listing', 'objects' );
		foreach ($taxonomy_objects as $tax) {
			if(isset($_REQUEST[ 'tax-'.$tax->name ] )) {
				$query_args[ 'tax-'.$tax->name ] = $_REQUEST[ 'tax-'.$tax->name ];
			}
        }
		
		$available_query_vars = $this->build_available_query_vars();
		foreach ($available_query_vars as $key => $meta_key) {

			if( isset($_REQUEST[ $meta_key ]) && $_REQUEST[ $meta_key ] != -1){

				$query_args[ $meta_key ] = $_REQUEST[ $meta_key ];	
				
			}

			
		}


		// add meta boxes support
		
		$orderby = isset($_REQUEST['truelysell_core_order']) ? $_REQUEST['truelysell_core_order'] : 'date';

	
		// if ( ! is_null( $featured ) ) {
		// 	$featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
		// }
		
	
		$listings = Truelysell_Core_Listing::get_real_listings( apply_filters( 'truelysell_core_output_defaults_args', $query_args ));
		$result = array(
			'found_listings'    => $listings->have_posts(),
			'max_num_pages' => $listings->max_num_pages,
		);

		ob_start();
		if ( $result['found_listings'] ) {
			$style_data = array(
				'style' 		=> $style, 
//				'class' 		=> $custom_class, 
				//'in_rows' 		=> $in_rows, 
				'grid_columns' 	=> $grid_columns,
				 'max_num_pages'	=> $listings->max_num_pages, 
				 'counter'		=> $listings->found_posts 
			);
			//$template_loader->set_template_data( $style_data )->get_template_part( 'listings-start' ); 
			?>
			<div class="loader-ajax-container" style=""> <div class="loader-ajax"></div> </div>
				<?php
				while ( $listings->have_posts() ) {
					$listings->the_post();
					
					$template_loader->set_template_data( $style_data )->get_template_part( 'content-listing',$style ); 	
					}
				?>
				<div class="clearfix"></div>
			</div>
			<?php
			//$template_loader->set_template_data( $style_data )->get_template_part( 'listings-end' ); 
		} else {
			?>
			<div class="loader-ajax-container" style=""> <div class="loader-ajax"></div> </div>
			<?php
			$template_loader->get_template_part( 'archive/no-found' ); 
			?><div class="clearfix"></div>
			<?php
		}
		
		$result['html'] = ob_get_clean();
		$result['pagination'] = truelysell_core_ajax_pagination( $listings->max_num_pages, absint( $_REQUEST['page'] ) );
	
		wp_send_json($result);
		
	}

	public function ajax_get_features_from_category(){
		
		$categories  = (isset($_REQUEST['cat_ids'])) ? $_REQUEST['cat_ids'] : '' ;

		$panel  =  (isset($_REQUEST['panel'])) ? $_REQUEST['panel'] : '' ;
		$success = true;
		ob_start();

		if($categories){
			$features = array();
			
			foreach ($categories as $category) {
				if(is_numeric($category)) {
					$cat_object = get_term_by('id', $category, 'listing_category');	
				} else {
					$cat_object = get_term_by('slug', $category, 'listing_category');	
				}
				if($cat_object){
					$features_temp = get_term_meta($cat_object->term_id,'truelysell_taxonomy_multicheck',true);
					if($features_temp) {
						$features = array_merge($features,$features_temp);
					}
					$features = array_unique($features);
					
				}
			}
			

			if($features){
				if($panel != 'false'){ ?>
					<div class="panel-checkboxes-container">
					<?php
						$groups = array_chunk($features, 4, true);
								
						foreach ($groups as $group) { ?>
							
							<?php foreach ($group as $feature) { 
								$feature_obj = get_term_by('slug', $feature, 'listing_feature'); 
								if( !$feature_obj ){
									continue;
								}
								?>
								<div class="panel-checkbox-wrap">
									<input form="truelysell_core-search-form" id="<?php echo esc_html($feature) ?>" value="<?php echo esc_html($feature) ?>" type="checkbox" name="tax-listing_feature[<?php echo esc_html($feature); ?>]">
									<label for="<?php echo esc_html($feature) ?>"><?php echo $feature_obj->name; ?></label>	
								</div>
							<?php } ?>
							

						<?php } ?>
					
					</div>
				<?php } else {

					foreach ($features as $feature) { 
						$feature_obj = get_term_by('slug', $feature, 'listing_feature');
						if( !$feature_obj ){
							continue;
						}?>
						<input form="truelysell_core-search-form" id="<?php echo esc_html($feature) ?>" value="<?php echo esc_html($feature) ?>" type="checkbox" name="tax-listing_feature[<?php echo esc_html($feature); ?>]">
						<label for="<?php echo esc_html($feature) ?>"><?php echo $feature_obj->name; ?></label>
					<?php }
				}
			} else { 
				if( $cat_object && isset($cat_object->name)) { 
					$success = false; ?>
				<div class="notification notice <?php if($panel){ echo "col-md-12"; } ?>">
					<p>
					<?php printf( __( 'Category "%s" doesn\'t have any additional filters', 'truelysell_core' ), $cat_object->name )  ?>
						
					</p>
				</div>
				<?php } else { 
					$success = false; ?>
			<?php }
				}
			} else {
			$success = false; ?>
			<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','truelysell_core') ?></p> </div>
		<?php }
				
		$result['output'] = ob_get_clean();
		$result['success'] = $success;
		wp_send_json($result);
	}
		
	public function ajax_get_features_ids_from_category(){
		
		$categories  = isset($_REQUEST['cat_ids']) ? $_REQUEST['cat_ids'] : false;
		$panel  =  $_REQUEST['panel'];
		$selected  =  isset($_REQUEST['selected']) ? $_REQUEST['selected'] : false;
		$listing_id  =  $_REQUEST['listing_id'];
		$success = true;
		if(!$selected){
			if($listing_id){
				$selected_check = wp_get_object_terms( $listing_id, 'listing_feature', array( 'fields' => 'ids' ) ) ;
				if ( ! empty( $selected_check ) ) {
					if ( ! is_wp_error( $selected_check ) ) {
						$selected = $selected_check;
					}
				}
			}
		};
		ob_start();

		if($categories){
		
			$features = array();
			foreach ($categories as $category) {
				if(is_numeric($category)) {
					$cat_object = get_term_by('id', $category, 'listing_category');	
				} else {
					$cat_object = get_term_by('slug', $category, 'listing_category');	
				}

				if($cat_object){
					$features_temp = get_term_meta( $cat_object->term_id, 'truelysell_taxonomy_multicheck', true );
					if($features_temp){
						foreach ($features_temp as $key => $value) {
							$features[] = $value;
						}
					}
					
					// if($features_temp) {
					// 	$features = $features + $features_temp;
					// }
				}
			}
			
			$features = array_unique($features);

			if($features){
				if($panel != 'false'){ ?>
					<div class="panel-checkboxes-container">
					<?php
						$groups = array_chunk($features, 4, true);
								
						foreach ($groups as $group) { ?>
							
							<?php foreach ($group as $feature) { 
								$feature_obj = get_term_by('slug', $feature, 'listing_feature'); 
								if( !$feature_obj ){
									continue;
								}
								
								?>
								<div class="panel-checkbox-wrap">
									<input form="truelysell_core-search-form"  value="<?php echo esc_html($feature_obj->term_id) ?>" type="checkbox" id="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>" name="tax_input[listing_feature][]" >
									<label for="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>"><?php echo $feature_obj->name; ?></label>	
								</div>
							<?php } ?>
							

						<?php } ?>
					
					</div>
				<?php } else {

	
					foreach ($features as $feature) { 
						$feature_obj = get_term_by('slug', $feature, 'listing_feature');
						if( !$feature_obj ){
							continue;
						}
						?>
						<input <?php if($selected) checked( in_array(  $feature_obj->term_id, $selected ) ); ?>value="<?php echo esc_html($feature_obj->term_id) ?>" type="checkbox" id="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>" name="tax_input[listing_feature][]" >
						<label id="label-in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>" for="in-listing_feature-<?php echo esc_html($feature_obj->term_id) ?>"><?php echo $feature_obj->name; ?></label>
					<?php }
				}
			} else { 
				if($cat_object){

				
				if( $cat_object->name) { 
					$success = false; ?>
				<div class="notification notice <?php if($panel){ echo "col-md-12"; } ?>">
					<p>
					<?php printf( __( 'Category "%s" doesn\'t have any additional filters', 'truelysell_core' ), $cat_object->name )  ?>
						
					</p>
				</div>
				<?php 
				}
			} else { 
					$success = false; ?>
				<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','truelysell_core') ?></p> </div>
			<?php }
				}
			} else {
			$success = false; ?>
			<div class="notification warning"><p><?php esc_html_e('Please choose category to display filters','truelysell_core') ?></p> </div>
		<?php }
				
		$result['output'] = ob_get_clean();
		$result['success'] = $success;
		wp_send_json($result);
	}

	public function ajax_get_listing_types_from_categories(){
		$categories  = isset($_REQUEST['cat_ids']) ? $_REQUEST['cat_ids'] : false;
		
		$success = true;
		$types = array();

		if($categories){
		
			
			foreach ($categories as $category) {
				if(is_numeric($category)) {
					$cat_object = get_term_by('id', $category, 'listing_category');	
				} else {
					$cat_object = get_term_by('slug', $category, 'listing_category');	
				}

				if($cat_object){
					$types_temp = get_term_meta( $cat_object->term_id, 'truelysell_taxonomy_type', true );
					if($types_temp){
						foreach ($types_temp as $key => $value) {
							$types[] = $value;
						}
					}
					
					
				}
			}
		}
		$result['output'] = $types;
		$result['success'] = $success;
		wp_send_json($result);
	}

	//sidebar
	public static function get_search_fields(){
		

		$currency_abbr = truelysell_fl_framework_getoptions('currency' );
  
  		$currency_symbol = Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
		$search_fields = array(
			
			'keyword_search' => array(
				'labeltext'	=> __( 'Keyword', 'truelysell_core' ),
				'placeholder'	=> __( 'What are you looking for?', 'truelysell_core' ),
				'key'			=> 'keyword_search',
				'class'			=> 'col-md-12',
				'name'			=> 'keyword_search',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'text',
			),	
			'location_search' => array(
				'labeltext'	=> __( 'Location', 'truelysell_core' ),
				'placeholder'	=> __( 'Location', 'truelysell_core' ),
				'key'			=> 'location_search',
				'class'			=> 'col-md-12',
				'css_class'		=> 'input-with-icon location',
				'name'			=> 'location_search',
		    	'priority'		=> 2,
		    	'place'			=> 'main',
				'type' 			=> 'location',
			),	
			  
			'category' => array(
				'labeltext'	=> __( 'Categories', 'truelysell_core' ),
				'placeholder'	=> __( 'All Categories', 'truelysell_core' ),
				'key'			=> '_category',
				'class'			=> 'col-md-12 ',
				'name'			=> 'tax-listing_category',
		    	'priority'		=> 5,
		    	'place'			=> 'main',
				'type' 			=> 'select-taxonomy',
				'taxonomy' 		=> 'listing_category',
			),	 
		);

		$fields = truelysell_core_sort_by_priority( apply_filters( 'truelysell_core_search_fields', $search_fields ) );

		return $fields;
	}

	public static function get_search_fields_half(){
		
		$search_fields = array(
			
			'keyword_search' => array(
				'placeholder'	=> __( 'What are you looking for?', 'truelysell_core' ),
				'key'			=> 'keyword_search',
				'class'			=> 'col-fs-6',
				'name'			=> 'keyword_search',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'text',
			),	
			'location_search' => array(
				'placeholder'	=> __( 'Location', 'truelysell_core' ),
				'key'			=> 'location_search',
				'class'			=> 'col-fs-6',
				'css_class'		=> 'input-with-icon location',				
				'name'			=> 'location_search',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'location',
			),	
			'category' => array(
				'placeholder'	=> __( 'Categories', 'truelysell_core' ),
				'key'			=> '_category',
				'name'			=> 'tax-listing_category',
				'type' 			=> 'multi-checkbox-row',
				'place'			=> 'panel',
				'taxonomy' 		=> 'listing_category',
			),
			'features' => array(
				'placeholder'	=> __( 'More Filters', 'truelysell_core' ),
				'key'			=> '_category',
				'name'			=> 'tax-listing_feature',
				'type' 			=> 'multi-checkbox-row',
				'place'			=> 'panel',
				'taxonomy' 		=> 'listing_feature',
				'dynamic' 		=> (get_option('truelysell_dynamic_features')=="on") ? "yes" : "no",
			),
			'radius' => array(
				'placeholder'	=> __( 'Distance Radius', 'truelysell_core' ),
				'key'			=> 'search_radius',
				'name'			=> 'search_radius',
				'type' 			=> 'radius',
				'place'			=> 'panel',
				'max' 			=> '100',
				'min' 			=> '1',	
			),	
			'price' => array(
				'placeholder'	=> __( 'Price Filter', 'truelysell_core' ),
				'key'			=> '',
				'name'			=> '_price',
				'type' 			=> 'slider',
				'place'			=> 'panel',
				'max' 			=> 'auto',
				'min' 			=> 'auto',
				
			),	
		
			'submit' => array(
				'class'			=> 'button fs-map-btn right',
				'open_row'		=> false,
				'close_row'		=> false,
				'place'			=> 'panel',
				'name' 			=> 'submit',
				'type' 			=> 'submit',
				'placeholder'	=> __( 'Search', 'truelysell_core' ),
			),			
		);
		if(is_post_type_archive('listing')){
			$top_buttons_conf = get_option('truelysell_listings_top_buttons_conf');
			if(get_option('pp_listings_top_layout') != 'half'){
				if(!in_array('filters',$top_buttons_conf)){
					unset($search_fields['features']);
					unset($search_fields['category']);
				}
				if(!in_array('radius',$top_buttons_conf)){
					unset($search_fields['radius']);
					
				}	
			}
			
		// 	'filters' (length=7)
  // 2 => string 'radius'
			
		}

		return apply_filters('truelysell_core_search_fields_half',$search_fields);
	}

	public static function get_search_fields_home(){
		
		$search_fields = array(
			// 'order' => array(
			// 	'placeholder'	=> __( 'Hidden order', 'truelysell_core' ),
			// 	'key'			=> 'truelysell_core_order',
			// 	'name'			=> 'truelysell_core_order',
		 //    	'place'			=> 'main',
			// 	'type' 			=> 'hidden',
			// ),	
			// 'search_radius' => array(
			// 	'placeholder'	=> __( 'Radius hidde', 'truelysell_core' ),
			// 	'key'			=> 'search_radius',
			// 	'name'			=> 'search_radius',
		 //    	'place'			=> 'main',
			// 	'type' 			=> 'hidden',
			// ),	
			'keyword_search1'  => array(
				'labeltext'     =>  __( 'What are you looking for?', 'truelysell' ),
				'label'         =>  __( 'What are you looking for?', 'truelysell' ),
				'class'         => 'form-control',
				'css_class'         => 'style1',
				'icon_class'     => 'feather-search',
				'id'            => 'keyword_search1',
				'placeholder'   => __( 'What are you looking for?', 'truelysell_core' ),
				'name'          => __( 'keyword_search1', 'truelysell_core' ),
				'key'           => 'keyword_search1',
				
				'default'       => '',
				'priority'      => 1,
				'place'         => 'main',
				'style'         => 'style1',
				'type'          => 'texticon',
			),   
			  'location_search1' => array(
				'labeltext'     =>  __( 'Location', 'truelysell' ),
 				'class'          => 'col-md-12 input-with-icon location',
				'css_class'         => 'style1',
				'placeholder'    => __( 'Location', 'truelysell_core' ),
				'key'            => 'location_search',
				'name'           => 'location_search1',
				'id'             => 'location_search1',
				'default'        => '',
				'icon_class'     => 'feather-map-pin',
				'priority'       => 2,
				'place'          => 'main',
				'style'          => 'style1',
				'type'           => 'locationhome',
			), 
			
		);

		return apply_filters('truelysell_core_search_fields_home',$search_fields);
	}

	public static function get_search_fields_home_box(){
		$currency_abbr = truelysell_fl_framework_getoptions('currency' );
  
  		$currency_symbol = Truelysell_Core_Listing::get_currency_symbol($currency_abbr);

		$search_fields = array(	
			'keyword_search1'  => array(
				'labeltext'     =>  __( 'What are you looking for?', 'truelysell' ),
				'label'         =>  __( 'What are you looking for?', 'truelysell' ),
				'class'         => 'form-control',
				'css_class'         => 'style1',
				'icon_class'     => 'feather-search',
				'id'            => 'keyword_search1',
				'placeholder'   => __( 'What are you looking for?', 'truelysell_core' ),
				'name'          => __( 'keyword_search1', 'truelysell_core' ),
				'key'           => 'keyword_search1',
				
				'default'       => '',
				'priority'      => 1,
				'place'         => 'main',
				'style'         => 'style1',
				'type'          => 'texticon',
			),   
			  'location_search1' => array(
				'labeltext'     =>  __( 'Location', 'truelysell' ),
 				'class'          => 'col-md-12 input-with-icon location',
				'css_class'         => 'style1',
				'placeholder'    => __( 'Location', 'truelysell_core' ),
				'key'            => 'location_search',
				'name'           => 'location_search1',
				'id'             => 'location_search1',
				'default'        => '',
				'icon_class'     => 'feather-map-pin',
				'priority'       => 2,
				'place'          => 'main',
				'style'          => 'style1',
				'type'           => 'locationhome',
			), 
			
	
		);

		return apply_filters('truelysell_core_search_fields_homebox',$search_fields);
	}

	
	public function output_search_form( $atts = array() ){
		extract( $atts = shortcode_atts( apply_filters( 'truelysell_core_output_defaults', array(
			'source'			=> 'sidebar', // home/sidebar/split
			'wrap_with_form'	=> 'yes',
			'custom_class' 		=> '',
			'action'			=> '',
			'more_trigger'		=> 'yes',
			'more_text_open'	=> __('Additional Features','truelysell_core'),
			'more_text_close'	=> __('Additional Features','truelysell_core'),
			'more_custom_class' => ' margin-bottom-10 margin-top-30',
			'more_trigger_style' => 'relative',
			'ajax_browsing'		=> truelysell_fl_framework_getoptions('ajax_browsing'),
			'dynamic_filters' 	=> (get_option('truelysell_dynamic_features')=="on") ? "on" : "off",
			'dynamic_taxonomies' => (get_option('truelysell_dynamic_taxonomies')=="on") ? "on" : "off",

		) ), $atts ) );

		switch ($source) {

			case 'home':
				$search_fields = $this->get_search_fields_home();
				//fix for panel slider for search
				if(isset($search_fields['_price'])){
					$search_fields['_price']['place'] = 'panel';
				}
				
				if(isset($search_fields['search_radius'])){
					$search_fields['search_radius']['place'] = 'panel';
				}
				break;

			case 'sidebar':
				$search_fields = $this->get_search_fields();
				
				break;

			case 'half':
				$search_fields = $this->get_search_fields_half();
				break;

			case 'homebox':
				$search_fields = $this->get_search_fields_home_box();

				break;
			
			default:
				$search_fields = $this->get_search_fields_home();	
				break;

		}

		
		if(isset($search_fields['tax-listing_feature'])){
			$search_fields['tax-listing_feature']['dynamic'] = (get_option('truelysell_dynamic_features')=="on") ? "yes" : "no";
		}
		if(isset($search_fields['features'])){
			$search_fields['features']['dynamic'] = (get_option('truelysell_dynamic_features')=="on") ? "yes" : "no";
		}

		$ajax = ($ajax_browsing == 'on') ? 'ajax-search' : truelysell_fl_framework_getoptions('ajax_browsing') ;
		if($ajax_browsing == 'on'){
			if(isset($search_fields['submit'])){
				unset($search_fields['submit']);	
			}
			
		}
		
		if(!get_option('truelysell_maps_api_server')){
				unset($search_fields['radius']);	
				unset($search_fields['search_radius']);	
		}
		if($source == 'home'){
			foreach ($search_fields as $key => $value) {
				if( in_array( $value['type'], array('multi-checkbox','multi-checkbox-row') ) ) {
					$search_fields[$key]['place'] = 'panel';
				}
				//place = panel
			}
		}
		//var_dump($search_fields);
		$template_loader = new Truelysell_Core_Template_Loader;

		//$action = get_post_type_archive_link( 'listing' );
		
		if(is_author()) {
			$author = get_queried_object();
    		$author_id = $author->ID;
			  $action = get_author_posts_url($author_id);
		}
 		
		ob_start();	
		if($wrap_with_form == 'yes') { 
			if($source == 'sidebar') {?>
		<div class="filter-head">
			
 <h5><?php echo esc_html_e('Filter by','truelysell_core'); ?></h5>
 <a href="#" class="reset-link"><?php echo esc_html_e('Reset Filters','truelysell_core'); ?></a>
		</div>
<?php } ?>
		<form action="<?php echo home_url(); ?>/grid-with-sidebar" id="truelysell_core-search-form" class="<?php if($dynamic_filters == 'on') { echo esc_attr('dynamic'); }  ?> <?php if($dynamic_taxonomies == 'on') { echo esc_attr('dynamic-taxonomies'); }  ?>  <?php echo esc_attr($custom_class) ?> <?php echo esc_attr($ajax) ?>" method="GET">
		<?php } 
		 
		if( in_array($source, array('home')) ) { ?>
			<div class="main-search-input-old">
		<?php }

			$more_trigger = false;
			$panel_trigger = false;
			foreach ($search_fields as $key => $value) {
				if( (isset($value['place']) && $value['place'] == 'adv'))  {
					$more_trigger = 'yes';
				}
				if( (isset($value['place']) && $value['place'] == 'panel'))  {
					$panel_trigger = 'yes';
				}
			}
			//count main fields
			$count = 0;
			foreach ($search_fields as $key => $value) {
				if(isset($value['place']) && $value['place'] == 'main') {
					$count++;
				}
			}
			$temp_count = 0;
			if($source == 'sidebar' || $source == 'home') {
				
			foreach ($search_fields as $key => $value) {
				 
				if( in_array($source, array('home','homebox')) && $value['type']!='hidden') { ?>
					<div class="search-input <?php if ($value['type']=='text') { ?> <?php echo esc_html_e('line','truelysell');  ?> <?php } else if ($value['type']=='texticon') { ?> <?php echo esc_html_e('line','truelysell');  ?> <?php } ?>">
				<?php }
				
				 
				if(isset($value['place']) && $value['place'] == 'main') {
 					//displays search form
					?>
 					<?php 
 						if($source == 'sidebar') { echo '<div class="with-forms" id="truelysell-search-form_'.$value['name'].'">'; }
 						if($value['type']=='select-taxonomy1' && in_array($source, array('home','homebox')) ) {
							
							$template_loader->set_template_data( $value )->get_template_part( 'search-form/select-taxonomy-home');

						} else {
							$template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);

						}
						if($source == 'sidebar') { echo '</div>'; }
  					if($value['type'] == 'radius') { ?>
						<div class="with-forms">
							<div class="col-md-12">
								<span class="panel-disable" data-disable="<?php echo esc_attr_e( 'Disable Radius', 'truelysell_core' ); ?>" data-enable="<?php echo esc_attr_e( 'Enable Radius', 'truelysell_core' ); ?>"><?php esc_html_e('Disable Radius', 'truelysell_core'); ?></span>
							</div>
						</div>
						
					<?php } 
				}

				if( in_array($source, array('home','homebox'))  ) { 
 				 
					//fix for price on home search
					if(isset($value['place']) && $value['place'] == 'panel') {
						?>
						<?php if( isset($value['type']) && $value['type'] != 'submit' ) { ?>
							<!-- Panel Dropdown -->
							<div class="panel-dropdown <?php if( $value['type'] == 'multi-checkbox-row') { echo "wide"; } if($value['type'] == 'radius') { echo 'radius-dropdown'; } ?> " id="<?php echo esc_attr( $value['name']); ?>-panel">
								<a href="#"><?php echo esc_html($value['placeholder']); ?></a>
								<div class="panel-dropdown-content <?php if( $value['type'] == 'multi-checkbox-row') { echo "checkboxes"; } ?> <?php if(isset($value['dynamic']) && $value['dynamic']=='yes'){ echo esc_attr('dynamic'); }?>">
							<?php } 
							
					 $template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']); 

							if( isset($value['type']) && $value['type'] != 'submit' ) { ?>
							<!-- Panel Dropdown -->
									<div class="panel-buttons">
										<?php if($value['type'] == 'radius') { ?>
											<span class="panel-disable" data-disable="<?php echo esc_attr_e( 'Disable', 'truelysell_core' ); ?>" data-enable="<?php echo esc_attr_e( 'Enable', 'truelysell_core' ); ?>"><?php esc_html_e('Disable','truelysell_core'); ?></span>
										<?php } else { ?>
											<span class="panel-cancel"><?php esc_html_e('Close', 'truelysell_core'); ?></span>
										<?php } ?>
										
										<button class="panel-apply"><?php esc_html_e('Apply', 'truelysell_core'); ?></button>
									</div>
								</div>
							</div>
						<?php }
						
					}
				}
				if( in_array($source, array('home','homebox'))  && $value['type']!='hidden') { ?>
					</div>
				<?php }
			} }
			?>
			
			<?php if($more_trigger == 'yes') : ?>				
				<!-- More Search Options -->
				<a href="#" class="more-search-options-trigger <?php echo esc_attr($more_custom_class) ?>" data-open-title="<?php echo esc_attr($more_text_open) ?>" data-close-title="<?php echo esc_attr($more_text_close) ?>"></a>
				<?php if($more_trigger_style == "over") : ?>
				<div class="more-search-options ">
					<div class="more-search-options-container">
				<?php else: ?>
					<div class="more-search-options relative">
				<?php endif; ?>

						<?php foreach ($search_fields as $key => $value) {
						if($value['place'] == 'adv') {

							//$template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);
						}
						} ?>
					<?php if($more_trigger_style == "over") : ?>
					</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if( $source!='home' && $panel_trigger == 'yes' ) { ?>
				<div class="row">
				<?php echo ($source=='half') ? '<div class="col-fs-12 panel-wrapper">' : '<div class="col-md-12  panel-wrapper">' ; {  ?>
					<?php 
					foreach ($search_fields as $key => $value) { 
						if($source != 'home' && isset($value['place']) && $value['place'] == 'panel') {
						?>
							
							<?php if( isset($value['type']) && !in_array($value['type'], array('submit','sortby')) ) { ?>
							<!-- Panel Dropdown -->
							<div class="panel-dropdown <?php if( $value['type'] == 'multi-checkbox-row') { echo "wide"; } if($value['type'] == 'radius') { echo 'radius-dropdown'; } ?> " id="<?php echo esc_attr( $value['name']); ?>-panel">
								<a href="#"><?php echo esc_html($value['placeholder']); ?></a>
								<div class="panel-dropdown-content <?php if( $value['type'] == 'multi-checkbox-row') { echo "checkboxes"; } ?> <?php if(isset($value['dynamic']) && $value['dynamic']=='yes'){ echo esc_attr('dynamic'); }?>">
							<?php } 
							
							$template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']); 

							if( isset($value['type']) && !in_array($value['type'], array('submit','sortby')) ) { ?>
							<!-- Panel Dropdown -->
									<div class="panel-buttons">
										<?php if($value['type'] == 'radius') { ?>
											<span class="panel-disable" data-disable="<?php echo esc_attr_e( 'Disable', 'truelysell_core' ); ?>" data-enable="<?php echo esc_attr_e( 'Enable', 'truelysell_core' ); ?>"><?php esc_html_e('Disable', 'truelysell_core'); ?></span>
										<?php } else { ?>
											<span class="panel-cancel"><?php esc_html_e('Close', 'truelysell_core'); ?></span>
										<?php } ?>
										
										<button class="panel-apply"><?php esc_html_e('Apply', 'truelysell_core'); ?></button>
									</div>
								</div>
							</div>
						<?php }
						}
					} ?>

				</div>
				</div>
			<?php }
			} ?>
 			<input type="hidden" name="action" value="truelysell_get_listings" />
			<!-- More Search Options / End -->
			<?php if($source == 'sidebar') {	?>
				<button class="button fullwidth btn btn-primary pl-5 pr-5 btn-block get_services w-100"><?php esc_html_e('Search','truelysell_core') ?></button>
			<?php } ?>

			<?php if(in_array($source, array('home','homebox')) ) { ?>
				<div class="search-btn">
					<button class="btn btn-primary search_service button" type="submit"><i class="feather-search me-2"></i> <?php esc_html_e('Search', 'truelysell_core') ?></button>
				</div>
			</div>
			<?php } ?>
		<?php if($wrap_with_form == 'yes') { ?>
		</form>
		<?php }
		//if ajax

 		$output = ob_get_clean();
 		echo $output;
	}



	public static function get_min_meta_value($meta_key = '',$type = '') {

		global $wpdb;
		$result = false;
		if(!empty($type)) {
			$type_query = 'AND ( m1.meta_key = "_listing_type" AND m1.meta_value = "'.$type.'")';
		} else {
			$type_query = false;
		}
		if($meta_key):
	
			$result = $wpdb->get_var(
		    $wpdb->prepare("
		            SELECT min(m2.meta_value + 0)
		            FROM $wpdb->posts AS p
		            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
					INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
					WHERE
					p.post_type = 'listing'
					AND p.post_status = 'publish'
					$type_query
					AND ( m2.meta_key IN ( %s, %s)  ) AND m2.meta_value != ''
		        ", $meta_key.'_min', $meta_key.'_max' )
		    ) ;
		    
		endif;

	    return $result;
	}	

	public static function get_max_meta_value($meta_key = '',$type = '' ) {
		global $wpdb;
		$result = false;
		if(!empty($type)) {
			$type_query = 'AND ( m1.meta_key = "_listing_type" AND m1.meta_value = "'.$type.'")';
		} else {
			$type_query = false;
		}
		if($meta_key):
		
			$result = $wpdb->get_var(
		    $wpdb->prepare("
		            SELECT max(m2.meta_value + 0)
		            FROM $wpdb->posts AS p
		            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
					INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
					WHERE
					p.post_type = 'listing'
					AND p.post_status = 'publish'
					$type_query
					AND ( m2.meta_key IN ( %s, %s)  ) AND m2.meta_value != ''
		        ", $meta_key.'_min', $meta_key.'_max' )
		    );
		  
	    endif;
	   

	    return $result;
	}


}