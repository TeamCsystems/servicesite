<?php
/**
 * Template Functions
 *
 * Template functions for listings
 *
 * @author 		Lukasz Girek
 * @version     1.0
 */


/**
 * Add custom body classes
 */
function truelysell_core_body_class( $classes ) {
	$classes   = (array) $classes;
	$classes[] = sanitize_title( wp_get_theme() );

	return array_unique( $classes );
}

add_filter( 'body_class', 'truelysell_core_body_class' );


/**
 * Outputs the listing offer type
 *
 * @return void
 */
function the_listing_offer_type( $post = null ) {
	$type = get_the_listing_offer_type( $post );
	$offers = truelysell_core_get_offer_types_flat(true);
	if(array_key_exists($type, $offers)) {
		echo '<span class="tag">'.$offers[$type].'</span>';	
	}
}


function truelysell_partition( $list, $p ) {
    $listlen = count( $list );
    $partlen = floor( $listlen / $p );
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice( $list, $mark, $incr );
        $mark += $incr;
    }
    return $partition;
}
/**
 * Gets the listing offer type
 *
 * @return string
 */
function get_the_listing_offer_type( $post = null ) {
	$post     = get_post( $post );
	if ( $post->post_type !== 'listing' ) {
		return;
	}
	return apply_filters( 'the_listing_offer_type', $post->_offer_type, $post );
}


function the_listing_type( $post = null ) {
	$type = get_the_listing_type( $post );
	$types = truelysell_core_get_listing_types(true);
	if(array_key_exists($type, $types)) {
		echo '<span class="listing-type-badge listing-type-badge-'.$type.'">'.$types[$type].'</span>';	
	}
}
/**
 * Gets the listing  type
 *
 * @return string
 */
function get_the_listing_type( $post = null ) {
	$post     = get_post( $post );
	if ( $post->post_type !== 'listing' ) {
		return;
	}
	return apply_filters( 'the_listing_type', $post->_listing_type, $post );
}

function truelysell_get_reviews_criteria(){
	$criteria = array(
		'service' => array(
				'label' => esc_html__('Service','truelysell_core'),
				'tooltip' => esc_html__('Quality of customer service and attitude to work with you','truelysell_core')
		), 
		   
	);

	return apply_filters('truelysell_reviews_criteria',$criteria);
}

/**
 * Outputs the listing location
 *
 * @return void
 */
function the_listing_address( $post = null ) {
	echo get_the_listing_address( $post );
}

/**
 * get_the_listing_address function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_listing_address( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'listing' ) {
		return;
	}
	
	$friendly_address = get_post_meta( $post->ID, '_friendly_address', true );
	$address = get_post_meta( $post->ID, '_address', true );
	$output =  (!empty($friendly_address)) ? $friendly_address : $address ;
	$disable_address = truelysell_fl_framework_getoptions('disable_address');
	if($disable_address){
		$output = get_post_meta( $post->ID, '_friendly_address', true );
	}
	return apply_filters( 'the_listing_location', $output, $post );
}

/*phone */

function the_listing_phone( $post = null ) {
	echo get_the_listing_phone( $post );
}

/**
 * get_the_listing_phone function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_listing_phone( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'listing' ) {
		return;
	}
	
	$phone = get_post_meta( $post->ID, '_phone', true );
	if($phone){
		$output = get_post_meta( $post->ID, '_phone', true );
	}
	return apply_filters( 'the_listing_phone', $output, $post );
}


/*phone */

/**
 * Outputs the listing price
 *
 * @return void
 */
function the_listing_price( $post = null ) {
	echo get_the_listing_price( $post );
}

/**
 * get_the_listing_price function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_listing_price( $post = null ) {
	return Truelysell_Core_Listing::get_listing_price( $post );
}


function get_the_listing_price_range( $post = null ) {
	return Truelysell_Core_Listing::get_listing_price_range( $post );
}


function truelysell_get_saved_icals( $post = null ) {
	return Truelysell_Core_iCal::get_saved_icals( $post );
} 

function truelysell_ical_export_url( $post_id = null ) {
	return Truelysell_Core_iCal::get_ical_export_url( $post_id );
} 

function truelysell_get_ical_events( $post_id = null ) {
	return Truelysell_Core_iCal::get_ical_events( $post_id );
} 





/**
 * Outputs the listing price per scale
 *
 * @return void
 */
function the_listing_price_per_scale( $post = null ) {
	echo get_the_listing_price_per_scale( $post );
}

function get_the_listing_price_per_scale( $post = null ) {
	return Truelysell_Core_Listing::get_listing_price_per_scale( $post );
}

function the_listing_location_link($post = null, $map_link = true ) {

	$address =  get_post_meta( $post, '_address', true );
	$friendly_address =  get_post_meta( $post, '_friendly_address', true );
	$disable_address = truelysell_fl_framework_getoptions('disable_address');
	if($disable_address) {
		echo $friendly_address;
	} else {
		if(empty($friendly_address)) { 
			$friendly_address = $address; 
		}
		
		if ( $address ) {
			if ( $map_link ) {
				// If linking to google maps, we don't want anything but text here
				echo apply_filters( 'the_listing_map_link', '<a class="listing-address popup-gmaps" href="' . esc_url( 'https://maps.google.com/maps?q=' . urlencode( strip_tags( $address ) ) . '' ) . '"><i class="fa fa-map-marker"></i>' . esc_html( strip_tags( $friendly_address ) ) . '</a>', $address, $post );
			} else {
				echo wp_kses_post( $friendly_address );
			}
		} 
	
	}
	
}


function truelysell_core_check_if_bookmarked($id){
	if($id){
		$classObj = new Truelysell_Core_Bookmarks;
		return $classObj->check_if_added($id);
	} else {
		return false;
	}
}

function truelysell_core_is_featured($id){
	$featured = get_post_meta($id,'_featured',true);
	if(!empty($featured)) {
		return true;
	} else {
		return false;
	}
}

function truelysell_core_is_instant_booking($id){
	$featured = get_post_meta($id,'_instant_booking',true);
	if(!empty($featured)) {
		return true;
	} else {
		return false;
	}
}



/**
 * Gets the listing title for the listing.
 *
 * @since 1.27.0
 * @param int|WP_Post $post (default: null)
 * @return string|bool|null
 */
function truelysell_core_get_the_listing_title( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'listing' !== $post->post_type ) {
		return;
	}

	$title = esc_html( get_the_title( $post ) );

	/**
	 * Filter for the listing title.
	 *
	 * @since 1.27.0
	 * @param string      $title Title to be filtered.
	 * @param int|WP_Post $post
	 */
	return apply_filters( 'truelysell_core_the_listing_title', $title, $post );
}

function truelysell_core_add_tooltip_to_label( $field_args, $field ) {
	// Get default label
	$label = $field->label();
	if ( $label && $field->options( 'tooltip' ) ) {
		$label = substr($label, 0, -9);
		
		// If label and tooltip exists, add it
		$label .= sprintf( ' <i class="tip" data-tip-content="%s"></i></label>',$field->options( 'tooltip' ) );
	}

	return $label;
}

/**
 * Overrides the default render field method
 * Allows you to add custom HTML before and after a rendered field
 *
 * @param  array             $field_args Array of field parameters
 * @param  CMB2_Field object $field      Field object
 */
function truelysell_core_render_as_col_12( $field_args, $field ) {

	// If field is requesting to not be shown on the front-end
	if ( ! is_admin() && ! $field->args( 'on_front' ) ) {
		return;
	}

	// If field is requesting to be conditionally shown
	if ( ! $field->should_show() ) {
		return;
	}

	$field->peform_param_callback( 'before_row' );

	echo '<div class="col-md-12">';
	
	// Remove the cmb-row class
	printf( '<div class="custom-class %s">', $field->row_classes() );

	if ( ! $field->args( 'show_names' ) ) {
	
		// If the field is NOT going to show a label output this
		$field->peform_param_callback( 'label_cb' );
	
	} else {

		// Otherwise output something different
		if ( $field->get_param_callback_result( 'label_cb', false ) ) {
			echo $field->peform_param_callback( 'label_cb' );
		}
		
	}

	$field->peform_param_callback( 'before' );
	
	// The next two lines are key. This is what actually renders the input field
	$field_type = new CMB2_Types( $field );
	$field_type->render();

	$field->peform_param_callback( 'after' );

		echo '</div>'; //cmb-row

	echo '</div>';

	$field->peform_param_callback( 'after_row' );

    // For chaining
	return $field;
}
/**
 * Dispays bootstarp column start
 * @param  string $col integer column width
 */
function truelysell_core_render_column($col='', $name='') {
	echo '<div class="col-md-'.$col.' form-field-'.$name.'-container">';
}

function truelysell_archive_buttons($list_style, $list_top_buttons = null){
	$template_loader = new Truelysell_Core_Template_Loader; 
	$data = array( 'buttons' => $list_top_buttons );
	$template_loader->set_template_data( $data )->get_template_part( 'archive/top-buttons' );
}

/* Hooks */
/* Hooks */
add_action( 'truelysell_before_archive', 'truelysell_archive_buttons', 25 ,2 );

/**
 * Return type of listings
 *
 */
function truelysell_core_get_listing_types(){
	 $options = array(
        	'service' => __( 'Service', 'truelysell_core' ),
			'rental' 	 => __( 'Rental', 'truelysell_core' ),
			'event' => __( 'Event', 'truelysell_core' ),
			
    );
	return apply_filters('truelysell_core_get_listing_types',$options);
}



/**
 * Return type of listings
 *
 */
function truelysell_core_get_rental_period(){
	 $options = array(
        	'daily' => __( 'Daily', 'truelysell_core' ),
			'weekly' 	 => __( 'Weekly', 'truelysell_core' ),
			'monthly' => __( 'Monthly', 'truelysell_core' ),
			'yearly' 	 => __( 'Yearly', 'truelysell_core' ),
    );
	return apply_filters('truelysell_core_get_rental_period',$options);
}

/**
 * Return type of offers
 *
 */

function truelysell_core_get_offer_types(){
	$options =  array(
        	'sale' => array( 
        		'name' => __( 'For Sale', 'truelysell_core' ),
        		'front' => '1'
        		), 
			'rent' => array( 
        		'name' => __( 'For Rent', 'truelysell_core' ),
        		'front' => '1'
        		), 
			'sold' => array( 
        		'name' => __( 'Sold', 'truelysell_core' )
        		), 
			'rented' => array( 
        		'name' => __( 'Rented', 'truelysell_core' )
        		), 
    );
	return apply_filters('truelysell_core_get_offer_types',$options);
}

function truelysell_core_get_offer_types_flat($with_all = false){
	$org_offer_types = truelysell_core_get_offer_types();

	$options = array();
	foreach ($org_offer_types as $key => $value) {

		if($with_all == true ) {
			$options[$key] = $value['name']; 
		} else {
			if(isset($value['front']) && $value['front'] == 1) {
				$options[$key] = $value['name']; 
			} elseif(!isset($value['front']) && in_array($key, array('sale','rent'))) {
					$options[$key] = $value['name']; 
				
			}
		}
	}
	return $options;
}
function truelysell_core_get_options_array($type,$data) {
	$options = array();
	if($type == 'taxonomy'){

		$args = array(
			'taxonomy' => $data,
			'hide_empty' => true,
			'orderby' => 'term_order'
		);		
		$args = apply_filters('truelysell_taxonomy_dropdown_options_args', $args);
		$categories =  get_terms( $data, $args  );	

		$options = array();
		foreach ($categories as $cat) {
			$options[$cat->term_id] = array ( 
				'name'  => $cat->name,
				'slug'  => $cat->slug,
				'id'	=> $cat->term_id,
				);
		}
	}
	return $options;
}
function truelysell_core_get_options_array_hierarchical($terms, $selected, $output = '', $parent_id = 0, $level = 0) {
    //Out Template
    $outputTemplate = '<option %SELECED% value="%ID%">%PADDING%%NAME%</option>';

    foreach ($terms as $term) {
        if ($parent_id == $term->parent) {
        	if(is_array($selected)) {
				$is_selected = in_array( $term->slug, $selected ) ? ' selected="selected" ' : '';
			} else {
				$is_selected = selected($selected, $term->slug, false);
			}
            //Replacing the template variables
            $itemOutput = str_replace('%SELECED%', $is_selected, $outputTemplate);
            $itemOutput = str_replace('%ID%', $term->slug, $itemOutput);
            $itemOutput = str_replace('%PADDING%', str_pad('', $level*12, '&nbsp;&nbsp;'), $itemOutput);
            $itemOutput = str_replace('%NAME%', $term->name, $itemOutput);

            $output .= $itemOutput;
            $output = truelysell_core_get_options_array_hierarchical($terms, $selected, $output, $term->term_id, $level + 1);
        }
    }
    return $output;
}


/**
 * Returns html for just options input based on data array
 *
 * @param  $data array
 */	
function get_truelysell_core_options_dropdown(  $data,$selected ){
	$output = '';

	if(is_array($data)) :
		foreach ($data as $id => $value) {
			if(is_array($selected)) {

				$is_selected = in_array( $value['slug'], $selected ) ? ' selected="selected" ' : '';
				
			} else {
				$is_selected = selected($selected, $id);
			}
			$output .= '<option '.$is_selected.' value="'.esc_attr($value['slug']).'">'.esc_html($value['name']).'</option>';
		}
	endif;
	return $output;
}

function get_truelysell_core_options_dropdown_by_type( $type, $data ){
	$output = '';
	if(is_array($data)) :
		foreach ($data as $id => $value) {
			$output .= '<option value="'.esc_attr($id).'">'.esc_html($value).'</option>';
		}
	endif;
	return $output;
}

function get_truelysell_core_numbers_dropdown( $number=10 ){
	$output = '';
	$x = 1;
	while($x <= $number) {
		$output .= '<option value="'.esc_attr($x).'">'.esc_html($x).'</option>';
    	$x++;
	} 
	return $output;
}

function get_truelysell_core_intervals_dropdown( $min, $max, $step = 100, $name = false ){
	$output = '';
	
	if($min == 'auto'){
		$min = Truelysell_Core_Search::get_min_meta_value($name);
	}
	if($max == 'auto'){
		$max = Truelysell_Core_Search::get_max_meta_value($name);
	}
	$range = range($min, $max, $step );
	if(sizeof($range) > 30 ) {
		$output = "<option>ADMIN NOTICE: increase your step value in Search Form Editor, having more than 30 steps is not recommended for performence options</option>";
	} else {
		foreach ($range as $number) {
		    $output .= '<option value="'.esc_attr($number).'">'.esc_html(number_format_i18n($number)).'</option>';
		}
	}
	return $output;
}


/**
 * Gets a number of posts and displays them as options
 * @param  array $query_args Optional. Overrides defaults.
 * @return array             An array of options that matches the CMB2 options array
 */
function truelysell_core_get_post_options( $query_args ) {

	$args = wp_parse_args( $query_args, array(
		'post_type'   => 'post',
		'numberposts' => -1,
	) );

	$posts = get_posts( $args );

	$post_options = array();
	$post_options[0] = esc_html__('--Choose page--','truelysell_core');
	if ( $posts ) {
		foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
		}
	}

	return $post_options;
}

/**
 * Gets 5 posts for your_post_type and displays them as options
 * @return array An array of options that matches the CMB2 options array
 */
function truelysell_core_get_pages_options() {
	return truelysell_core_get_post_options( array( 'post_type' => 'page', ) );
}


function truelysell_core_get_listing_packages_as_options() {
	
	$args =  array(
			'post_type'        => 'product',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'date',
			'suppress_filters' => false,
			'tax_query'        => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'listing_package'),
					'operator' => 'IN',
				),
			),
			
	);

	$posts = get_posts( $args );

	$post_options = array();
	
	if ( $posts ) {
		foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
		}
	}

	return $post_options;
}

function truelysell_core_get_listing_taxonomies_as_options() {
	$taxonomy_objects = get_object_taxonomies( 'listing', 'objects' );

	$_options = array();
	
	if ( $taxonomy_objects ) {
		foreach ( $taxonomy_objects as $tax ) {
          $_options[ $tax->name ] = $tax->label;
		}
	}

	return $_options;
}
function truelysell_core_get_product_taxonomies_as_options() {
	$taxonomy_objects = get_terms(array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	));

	$_options = array();
	
	if ( $taxonomy_objects ) {
		foreach ( $taxonomy_objects as $tax ) {
			
          $_options[ $tax->term_id ] = $tax->name;
		}
	}
	

	return $_options;
}




function truelysell_core_agent_name(){
	$fname = get_the_author_meta('first_name');
	$lname = get_the_author_meta('last_name');
	$full_name = '';

	if( empty($fname)){
	    $full_name = $lname;
	} elseif( empty( $lname )){
	    $full_name = $fname;
	} else {
	    //both first name and last name are present
	    $full_name = "{$fname} {$lname}";
	}

	echo $full_name;
}


function truelysell_core_ajax_pagination($pages = '', $current = false, $range = 2 ) {
    if(!empty($current)){
        $paged = $current;
    } else {
        global $paged;  
    }
    
    $output = false;
    if(empty($paged)) $paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ( $range * 2 )+1;
    $range = 2; // change it to show more links

    if( $pages == '' ){
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if( !$pages ){
            $pages = 1;
        }
    }

    if( 1 != $pages ){

        
            $output .= '<nav class="pagination margin-top-30"><ul class="pagination">';
                 $output .=  ( $paged > 1 ) ? '<li class="arrow" data-paged="prev"><a class="previouspostslink" href="#"">'.__('<i class="fas fa-angle-left"></i>','truelysell_core').'</a></li>' : '';
                for ( $i = 1; $i <= $pages; $i++ ) {
                    if ( 1 != $pages &&( !( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems ) )
                    {
                        if ( $paged == $i ){
                            $output .=  '<li class="current" data-paged="'.$i.'"><a href="#">'.$i.' </a></li>';
                        } else {
                            $output .=  '<li data-paged="'.$i.'"><a href="#">'.$i.'</a></li>';
                        }
                    }
                }
                $output .=  ( $paged < $pages ) ? '<li class="arrow" data-paged="next"><a class="nextpostslink" href="#">'.__('<i class="fas fa-angle-right"></i>','truelysell_core').'</a></li>' : '';
            $output .=  '</ul></nav>';
      }
    return $output;
}
function truelysell_core_pagination($pages = '', $current = false, $range = 2 ) {
    if(!empty($current)){
    	$paged = $current;
    } else {
    	global $paged;	
    }
    

    if(empty($paged))$paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ( $range * 2 )+1;
    $range = 2; // change it to show more links

    if( $pages == '' ){
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if( !$pages ){
            $pages = 1;
        }
    }

    if( 1 != $pages ){

        
            echo '<ul>';
                echo ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) ? '<li  class="pagination_arrow"><a href="'.get_pagenum_link(1).'"><i class="fas fa-angle-left"></i> PREV</a></li>' : '';
                for ( $i = 1; $i <= $pages; $i++ ) {
                    if ( 1 != $pages &&( !( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems ) )
                    {
                        if ( $paged == $i ){
                            echo '<li class="active" data-paged="'.$i.'"><a class="fhdjh" href="'.get_pagenum_link($i).'">'.$i.' </a></li>';
                        } else {
                            echo '<li data-paged="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
                        }
                    }
                }
                echo ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) ? '<li class="pagination_arrow"><a  href="'.get_pagenum_link( $pages ).'"><i class="fas fa-angle-right"></i></a></li>' : '';
            echo '</ul>';
  

    }
}

function truelysell_core_get_post_status($id){
	$status = get_post_status($id);
	switch ($status) {
		case 'publish':
			$friendly_status = esc_html__('Published', 'truelysell_core');
			break;		
		case 'pending_payment':
			$friendly_status = esc_html__('Pending Payment', 'truelysell_core');
			break;
		case 'expired':
			$friendly_status = esc_html__('Expired', 'truelysell_core');
			break;
		case 'draft':
		case 'pending':
			$friendly_status = esc_html__('Pending Approval', 'truelysell_core');
			break;
		
		default:
			$friendly_status = $status;
			break;
	}
	return $friendly_status;
	
}

/**
 * Calculates and returns the listing expiry date.
 *
 * @since 1.22.0
 * @param  int $id
 * @return string
 */
function calculate_listing_expiry( $id ) {
	// Get duration from the product if set...
	$duration = get_post_meta( $id, '_duration', true );

	// ...otherwise use the global option
	if ( ! $duration ) {
		$duration = absint( truelysell_fl_framework_getoptions('default_duration') );
	}

	if ( $duration > 0) {
		$new_date = date_i18n( 'Y-m-d', strtotime( "+{$duration} days", current_time( 'timestamp' ) ) );
		return CMB2_Utils::get_timestamp_from_value( $new_date , 'm/d/Y' );	
	}

	return '';
}

function truelysell_core_get_expiration_date($id) {
	$expires = get_post_meta( $id, '_listing_expires', true );

	$package_id = get_post_meta( $id, '_user_package_id', true );

	if($package_id){
	    global $wpdb;
	   $id = $wpdb->get_var( 
		$wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}truelysell_core_user_packages WHERE
		    id = %d",
		    $package_id
		));

		if($id && function_exists('wcs_get_subscription') ){
		  
		  $subscription_obj = wcs_get_subscription($id);
		  if($subscription_obj ){
			  $date_end =  $subscription_obj->get_date( 'end' );

			  if(!empty($date_end)){

				  $converted_date = date_i18n( get_option( 'date_format' ), strtotime($date_end ));
				  return $converted_date;
			  } else {

					if(!empty($expires)) {
						if(truelysell_core_is_timestamp($expires)) {
							$saved_date = get_option( 'date_format' );
							$new_date = date_i18n($saved_date, $expires); 
						} else {
							return $expires;
						}
					}
			  }
		  }
		  
		}
	}
	
	if(!empty($expires)) {
		if(truelysell_core_is_timestamp($expires)) {
			$saved_date = get_option( 'date_format' );
			$new_date = date_i18n($saved_date, $expires); 
		} else {
			return $expires;
		}
	}
	return (empty($expires)) ? __('Never/not set','truelysell_core') : $new_date ;
}

function truelysell_core_is_timestamp($timestamp) {

		$check = (is_int($timestamp) OR is_float($timestamp))
			? $timestamp
			: (string) (int) $timestamp;
		return  ($check === $timestamp)
	        	AND ( (int) $timestamp <=  PHP_INT_MAX)
	        	AND ( (int) $timestamp >= ~PHP_INT_MAX);
	}
	
function truelysell_core_get_listing_image($id){
	if(has_post_thumbnail($id)){ 
		return	wp_get_attachment_image_url( get_post_thumbnail_id( $id ),'truelysell-listing-grid' );
	} else {
		$gallery = (array) get_post_meta( $id, '_gallery', true );

		$ids = array_keys($gallery);
		if(!empty($ids[0]) && $ids[0] !== 0){ 
			return  wp_get_attachment_image_url($ids[0],'truelysell-listing-grid'); 
		} else {
			$placeholder = get_truelysell_core_placeholder_image();
			return $placeholder;
		}
	} 
}

add_action('truelysell_page_subtitle','truelysell_core_my_account_hello');
function truelysell_core_my_account_hello(){
	$my_account_page = get_option( 'my_account_page');
	if(is_user_logged_in() && !empty($my_account_page) && is_page($my_account_page)){
		$current_user = wp_get_current_user();
		if(!empty($current_user->user_firstname)){
			$name = $current_user->user_firstname.' '.$current_user->user_lastname;
		} else {
			$name = $current_user->display_name;
		}
		echo "<span>" . esc_html__('Howdy, ','truelysell_core') . $name.'!</span>';
	} else {
		global $post;
		$subtitle = get_post_meta($post->ID,'truelysell_subtitle',true);
		if($subtitle) {
			echo "<span>".esc_html($subtitle)."</span>";
		}
	}
}



function truelysell_core_sort_by_priority( $array = array(), $order = SORT_NUMERIC ) {
	
		if ( ! is_array( $array ) )
			return;

		// Sort array by priority

		$priority = array();

		foreach ( $array as $key => $row ) {

			if ( isset( $row['position'] ) ) {
				$row['priority'] = $row['position'];
				unset( $row['position'] );
			}

			$priority[$key] = isset( $row['priority'] ) ? absint( $row['priority'] ) : false;
		}

		array_multisort( $priority, $order, $array );

		return apply_filters( 'truelysell_sort_by_priority', $array, $order );
}

/**
 * CMB2 Select Multiple Custom Field Type
 * @package CMB2 Select Multiple Field Type
 */

/**
 * Adds a custom field type for select multiples.
 * @param  object $field             The CMB2_Field type object.
 * @param  string $value             The saved (and escaped) value.
 * @param  int    $object_id         The current post ID.
 * @param  string $object_type       The current object type.
 * @param  object $field_type_object The CMB2_Types object.
 * @return void
 */
if(!function_exists('cmb2_render_select_multiple_field_type')) {
	function cmb2_render_select_multiple_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$saved_values = get_post_meta($object_id,$field->args['_name']);

		$select_multiple = '<select class="widefat" multiple name="' . $field->args['_name'] . '[]" id="' . $field->args['_id'] . '"';
		foreach ( $field->args['attributes'] as $attribute => $value ) {
			$select_multiple .= " $attribute=\"$value\"";
		}
		$select_multiple .= ' />';
		
		if(is_string($escaped_value)) {
			$escaped_value = explode(',',$escaped_value);
		} 
		foreach ( $field->options() as $value => $name ) {
			$selected = '';
			if(is_array($saved_values)){

				if(in_array($value,$saved_values)) {
					$selected = 'selected="selected"';
				}
			} else {
				$selected = ( $escaped_value && in_array( $value, $escaped_value ) ) ? 'selected="selected"' : '';	
			}
			
			
			$select_multiple .= '<option class="cmb2-option" value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		}

		$select_multiple .= '</select>';
		$select_multiple .= $field_type_object->_desc( true );

		echo $select_multiple; // WPCS: XSS ok.
	}
	add_action( 'cmb2_render_select_multiple', 'cmb2_render_select_multiple_field_type', 10, 5 );


	/**
	 * Sanitize the selected value.
	 */
	
	function cmb2_sanitize_select_multiple_callback( $override_value, $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $saved_value ) {
				$value[$key] = sanitize_text_field( $saved_value );
			}
			return $value;
		}
		return;
	}
	add_filter( 'cmb2_sanitize_select_multiple', 'cmb2_sanitize_select_multiple_callback', 10, 4 );

	

	function cmb2_save_select_multiple_callback( $override, array $args, array  $field_args ) {
		if($field_args['type'] == 'select_multiple' || $field_args['type'] === 'multicheck_split') {
			if ( is_array( $args['value'] ) ) {
			
				delete_post_meta($args['id'], $args['field_id']);
				foreach ( $args['value'] as $key => $saved_value ) {
					$sanitized_value = sanitize_text_field( $saved_value );
					add_post_meta( $args['id'], $args['field_id'], $sanitized_value );
				}

				
			}
			return true;
		}
		return $override;
		
	}
	add_filter( 'cmb2_override_meta_save', 'cmb2_save_select_multiple_callback', 10, 4 );


}
function cmb2_render_multicheck_split_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	$saved_values = get_post_meta($object_id,$field->args['_name']);

	$select_multiple = '
	<ul class="cmb2-checkbox-list cmb2-list">	';
	
	
	if(is_string($escaped_value)) {
		$escaped_value = explode(',',$escaped_value);
	} 
	$i = 0;
	foreach ( $field->options() as $value => $name ) {
		$selected = '';
		$i++;
		if(is_array($saved_values)){
			if(in_array($value,$saved_values)) {
				$selected = 'checked="checked"';
			}
		} else {
			$selected = ( $escaped_value && in_array( $value, $escaped_value ) ) ? 'checked="checked"' : '';	
		}	
		
		$select_multiple .= '<li><input type="checkbox" class="cmb2-option" name="' . $field->args['_name'] . '[]" id="' . $field->args['_id'] . $i .'" value="' . esc_attr( $value ) . '" ' . $selected . '><label for="' . $field->args['_id'] . $i .'">' . esc_html( $name ) . '</label></li>';
	}
	$select_multiple .= "</ul>";
	
	$select_multiple .= $field_type_object->_desc( true );

	echo $select_multiple; // WPCS: XSS ok.
}
add_action( 'cmb2_render_multicheck_split', 'cmb2_render_multicheck_split_field_type', 5, 5 );

function truelysell_core_array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}


function truelysell_core_get_nearby_listings($lat, $lng, $distance, $radius_type){
    global $wpdb;
    if($radius_type=='km') {
    	$ratio = 6371;
    } else {
    	$ratio = 3959;
    }

  	$post_ids = 
			$wpdb->get_results(
				$wpdb->prepare( "
			SELECT DISTINCT
			 		geolocation_lat.post_id,
			 		geolocation_lat.meta_key,
			 		geolocation_lat.meta_value as listingLat,
			        geolocation_long.meta_value as listingLong,
			        ( %d * acos( cos( radians( %f ) ) * cos( radians( geolocation_lat.meta_value ) ) * cos( radians( geolocation_long.meta_value ) - radians( %f ) ) + sin( radians( %f ) ) * sin( radians( geolocation_lat.meta_value ) ) ) ) AS distance 
		       
			 	FROM 
			 		$wpdb->postmeta AS geolocation_lat
			 		LEFT JOIN $wpdb->postmeta as geolocation_long ON geolocation_lat.post_id = geolocation_long.post_id
					WHERE geolocation_lat.meta_key = '_geolocation_lat' AND geolocation_long.meta_key = '_geolocation_long'
			 		HAVING distance < %d

		 	", 
		 	$ratio, 
		 	$lat, 
		 	$lng, 
		 	$lat, 
		 	$distance)
		,ARRAY_A);

    return $post_ids;
 
}


function truelysell_core_geocode($address){
 
    // url encode the address
    $address = urlencode($address);
	$geocoding_provider = get_option('truelysell_geocoding_provider','google');
	if($geocoding_provider == 'google'){
		$api_key = get_option( 'truelysell_maps_api_server' );	
		// google map geocode api url
	    $url = "https://maps.google.com/maps/api/geocode/json?address={$address}&key={$api_key}";
	 
	    // get the json response
	  	$resp_json = wp_remote_get($url);
	  	
	 	$resp = json_decode( wp_remote_retrieve_body( $resp_json ), true );

	    // response status will be 'OK', if able to geocode given address 
	    if($resp['status']=='OK'){
	 
	        // get the important data
	        $lati = $resp['results'][0]['geometry']['location']['lat'];
	        $longi = $resp['results'][0]['geometry']['location']['lng'];
	        $formatted_address = $resp['results'][0]['formatted_address'];
	         
	        // verify if data is complete
	        if($lati && $longi && $formatted_address){
	         
	            // put the data in the array
	            $data_arr = array();            
	             
	            array_push(
	                $data_arr, 
	                    $lati, 
	                    $longi, 
	                    $formatted_address
	                );
	             
	            return $data_arr;
	             
	        }else{
	            return false;
	        }
	         
	    }else{
	        return false;
	    }


	} else {
		$api_key = get_option( 'truelysell_geoapify_maps_api_server' );	
		$url = "https://api.geoapify.com/v1/geocode/search?text={$address}&apiKey={$api_key}";
	 
	    // get the json response
	  	$resp_json = wp_remote_get($url);
	  	
	 	$resp = json_decode( wp_remote_retrieve_body( $resp_json ), true );
	 
	    // response status will be 'OK', if able to geocode given address 
	    if($resp){
	 
	        // get the important data
	        $lati = $resp['features'][0]['geometry']['coordinates'][1];
	        $longi = $resp['features'][0]['geometry']['coordinates'][0];
	        $formatted_address = $resp['features'][0]['properties']['formatted'];
	         
	        // verify if data is complete
	        if($lati && $longi && $formatted_address){
	         
	            // put the data in the array
	            $data_arr = array();            
	             
	            array_push(
	                $data_arr, 
	                    $lati, 
	                    $longi, 
	                    $formatted_address
	                );
	             
	            return $data_arr;
	             
	        }else{
	            return false;
	        }
	         
	    }else{
	        return false;
	    }
	}
	
    
}

function truelysell_core_get_place_id($post){
   // url encode the address


    $address = urlencode(get_post_meta($post->ID, '_address',true));
	$api_key = get_option( 'truelysell_maps_api_server' );
    // google map geocode api url
    $url = "https://maps.google.com/maps/api/geocode/json?address={$address}&key={$api_key}";
 
    // get the json response
  	$resp_json = wp_remote_get($url);
  	
 	$resp = json_decode( wp_remote_retrieve_body( $resp_json ), true );

    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
      
       if(isset($resp['results'][0]['place_id'])){
         
     	return $resp['results'][0]['place_id'];
       
       } else  {

       	return false;
       
       }
         
    }else{
        return false;
    }
}

function truelysell_get_google_reviews($post){
	$reviews = false;
	if(get_option('truelysell_google_reviews')) {

		
		if ( get_transient('truelysell_reviews_'.$post->ID) ) {
			$reviews =  get_transient('truelysell_reviews_'.$post->ID);
		} else {
			
			$api_key = get_option( 'truelysell_maps_api_server' );
			$place_id = get_post_meta($post->ID, '_place_id',true);
			$language = get_option('truelysell_google_reviews_lang','en');
			$url = "https://maps.googleapis.com/maps/api/place/details/json?key={$api_key}&placeid={$place_id}&language={$language}";
			
			$resp_json = wp_remote_get($url);
			
			$reviews = json_decode( wp_remote_retrieve_body( $resp_json ), true );
			$cache_time  = get_option('truelysell_google_reviews_cache_days',1);
			set_transient( 'truelysell_reviews_'.$post->ID, $reviews, $cache_time * 24 * HOUR_IN_SECONDS );
			
		}
	}

	return $reviews;
}

/**
 * Checks if the user can edit a listing.
 */
function truelysell_core_if_can_edit_listing( $listing_id ) {
	$can_edit = true;

	if ( ! is_user_logged_in() || ! $listing_id ) {
		$can_edit = false;
	} else {
		$listing      = get_post( $listing_id );

		if ( ! $listing || ( absint( $listing->post_author ) !== get_current_user_id()  ) ) {
			$can_edit = false;
		}
		
	}

	return apply_filters( 'truelysell_core_if_can_edit_listing', $can_edit, $listing_id );
}



//&& ! current_user_can( 'edit_post', $listing_id )


add_filter('submit_listing_form_submit_button_text','truelysell_core_rename_button_no_preview');

function truelysell_core_rename_button_no_preview(){
	if(get_option('truelysell_new_listing_preview' )) {
			return  __( 'Submit', 'truelysell_core' );
		} else {
			return  __( 'Preview', 'truelysell_core' );
		}
}

function get_truelysell_core_placeholder_image(){
	$image_id = get_option('truelysell_placeholder_id' );
	
	if($image_id) {
		//$placeholder = wp_get_attachment_image_src($image_id,'truelysell-listing-grid');
		return $image_id;
	} else {
		return  plugin_dir_url( __FILE__ )."assets/images/truelysell_placeholder.png";
	}
	
}


function truelysell_is_rated() {
	return true;
}



function truelysell_post_view_count(){
	if ( is_single() ){

		global $post;
		$count_post 	= get_post_meta( $post->ID, '_listing_views_count', true);
		$author_id 		= get_post_field( 'post_author', $post->ID );

		$total_views 	= get_user_meta($author_id,'truelysell_total_listing_views',true);

		if( $count_post == ''){
		
			$count_post = 1;
			add_post_meta( $post->ID, '_listing_views_count', $count_post);
			
			$total_views = (int) $total_views + 1;
			update_user_meta($author_id, 'truelysell_total_listing_views', $total_views);
			
		} else {
		
			$total_views = (int) $total_views + 1;
			update_user_meta($author_id, 'truelysell_total_listing_views', $total_views);

			$count_post = (int)$count_post + 1;
			update_post_meta( $post->ID, '_listing_views_count', $count_post);
		
		}
	}
}
add_action('wp_head', 'truelysell_post_view_count');

function truelysell_count_user_comments( $args = array() ) {
    global $wpdb;
    $default_args = array(
        'author_id' => 1,
        'approved' => 1,
        'author_email' => '',
    );

    $param = wp_parse_args( $args, $default_args );
    
    $sql = $wpdb->prepare( "SELECT COUNT(comments.comment_ID) 
            FROM {$wpdb->comments} AS comments 
            LEFT JOIN {$wpdb->posts} AS posts
            ON comments.comment_post_ID = posts.ID
            WHERE posts.post_author = %d
            AND comment_approved = %d
            AND comment_author_email NOT IN (%s)
            AND comment_type IN ('comment', '')",
        $param
    );

    return $wpdb->get_var( $sql );
}




if ( ! function_exists( 'truelysell_comment_review' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since astrum 1.0
 */
function truelysell_comment_review( $comment, $args, $depth ) {
  $GLOBALS['comment'] = $comment;
  global $post;

  switch ( $comment->comment_type ) :
    case 'pingback' :
    case 'trackback' :
  ?>
  <li class="post pingback">
    <p><?php esc_html_e( 'Pingback:', 'truelysell_core' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( esc_html__( '(Edit)', 'truelysell' ), ' ' ); ?></p>
  <?php
      break;
    default :
      $allowed_tags = wp_kses_allowed_html( 'post' );
      $rating  = get_comment_meta( get_comment_ID(), 'truelysell-rating', true ); 
  ?>
		
		<li class="review-box">

		<div class="review-profile 2">
										<div class="review-img">
										 <?php echo get_avatar( $comment->comment_author_email, 70 ); ?> 
  											<div class="review-name">
 												<?php if( $comment->user_id === $post->post_author ) { 
				?>
					<h6><?php echo $comment->comment_author; ?></h6>
				<?php } else {
					printf( '<h6>%s</h6>', get_comment_author_link() ); 
				} ?> 
												<p><?php printf( esc_html__( '%1$s at %2$s', 'truelysell_core' ), get_comment_date(), get_comment_time() ); ?></p>
											</div>
										</div>
										<div class="rating 2">	
										<div class="star-rating" data-rating="<?php echo esc_attr($rating); ?>"></div>
										</div>
									</div>

									<?php comment_text(); ?>

       	
		 
	 
		<div class="comment-content">
			<?php 
	            $photos = get_comment_meta( get_comment_ID(), 'truelysell-attachment-id', false );

	            if($photos) : ?>
	            <div class="review-images mfp-gallery-container">
	            	<?php foreach ($photos as $key => $attachment_id) {

	            		$image = wp_get_attachment_image_src( $attachment_id, 'truelysell-gallery' );
	            		$image_thumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

	            	 ?>
					<a href="<?php echo esc_attr($image[0]); ?>" class="mfp-gallery"><img src="<?php echo esc_attr($image_thumb[0]); ?>" alt=""></a>
					<?php } ?>
				</div>
				<?php endif; ?>
			<?php $review_rating = get_comment_meta( get_comment_ID(), 'truelysell-review-rating', true ); ?>
			
        </div>
					</li>

  <?php
      break;
  endswitch;
}
endif; // ends check for truelysell_comment()

function truelysell_get_days(){
	$start_of_week = intval( get_option( 'start_of_week' ) ); // 0 - sunday, 1- monday

	$days = array(
		'monday'	=> __('Monday','truelysell_core'),
		'tuesday' 	=> __('Tuesday','truelysell_core'),
		'wednesday' => __('Wednesday','truelysell_core'),
		'thursday' 	=> __('Thursday','truelysell_core'),
		'friday' 	=> __('Friday','truelysell_core'),
		'saturday' 	=> __('Saturday','truelysell_core'),
		'sunday' 	=> __('Sunday','truelysell_core'),
	);

	if($start_of_week == 0){
	
		$sun['sunday'] = __('Sunday','truelysell_core');
		$days = $sun + $days;
	}
	return apply_filters( 'truelysell_days_array',$days );
	
}

function truelysell_top_comments_only( $clauses )
{
    $clauses['where'] .= ' AND comment_parent = 0';
    return $clauses;
}

function truelysell_check_if_review_replied($comment_id,$user_id){

	$author_replies_args = array(
			'user_id' => $user_id,
			'parent'  => $comment_id
		);
	$author_replies = get_comments( $author_replies_args ); 
	return (empty($author_replies)) ? false : true ;
}
function truelysell_get_review_reply($comment_id,$user_id){

	$author_replies_args = array(
			'user_id' => $user_id,
			'parent'  => $comment_id
		);
	$author_replies = get_comments( $author_replies_args ); 
	return $author_replies;
}


function truelysell_check_if_open($post = ''){

	$status = false;
	$has_hours = false;
	if(empty($post)){
		global $post;	
	}
	
	$days = truelysell_get_days();
	$storeSchedule = array();
	foreach ($days as $d_key => $value) {
		$open_val = get_post_meta($post->ID, '_'.$d_key.'_opening_hour', true);

		$opening = ($open_val) ? $open_val : '' ;
		$clos_val = get_post_meta($post->ID, '_'.$d_key.'_closing_hour', true);
		$closing = ($clos_val) ? $clos_val : '';
		
		
		
		$storeSchedule[$d_key] = array(
			'opens' => $opening,
			'closes' => $closing
		);
		
	}

	$clock_format = truelysell_fl_framework_getoptions('clock_format');

    //get current  time
    $meta_timezone =  get_post_meta($post->ID, '_listing_timezone', true);

    if(empty($meta_timezone)) {

	    $timeObject = new DateTime(null, truelysell_get_timezone());
	    $timestamp 		= $timeObject->getTimeStamp();
		$currentTime 	= $timeObject->setTimestamp($timestamp)->format('Hi');
			
    } else {
    	
    	if(substr($meta_timezone,0,3) == "UTC"){
     		$offset =  substr($meta_timezone, 3);
 			$meta_timezone = str_replace('UTC','Etc/GMT',$meta_timezone);
    		if ( 0 == $offset ) {
				
			} elseif ( $offset < 0 ) {
				$meta_timezone = str_replace('-','+',$meta_timezone);
			} else {
				$meta_timezone = str_replace('+','-',$meta_timezone);
				
			}
			

    	}
 
   
	    date_default_timezone_set($meta_timezone);
	    $timeObject = new DateTime(null);
	    $timestamp 		= $timeObject->getTimeStamp();
	    $currentTime 	= $timeObject->setTimestamp($timestamp)->format('Hi');
    }
    
	if(isset($storeSchedule[lcfirst(date('l', $timestamp))])) :


		$day = ($storeSchedule[lcfirst(date('l', $timestamp))]);

		$startTime = $day['opens'];
		$endTime = $day['closes'];
		if(is_array($startTime)){
			foreach ($startTime as $key => $start_time) {
				# code...
			$end_time = $endTime[$key];
			if(!empty($start_time) && is_numeric(substr($start_time, 0, 1)) ) {
				if(substr($start_time, -1)=='M'){


					$start_time = DateTime::createFromFormat('h:i A', $start_time);
					if($start_time){
						$start_time = $start_time->format('Hi');			
					}

					//
				} else {
					$start_time = DateTime::createFromFormat('H:i', $start_time);
					if($start_time){
						$start_time = $start_time->format('Hi');
					}
				}
				
		 	} 
		       //create time objects from start/end times and format as string (24hr AM/PM)
			if(!empty($end_time)  && is_numeric(substr($end_time, 0, 1))){
				if(substr($end_time, -1)=='M'){
					$end_time = DateTime::createFromFormat('h:i A', $end_time);			
					if($end_time){
						$end_time = $end_time->format('Hi');
					}
				} else {
					$end_time = DateTime::createFromFormat('H:i', $end_time);
					if($end_time){
						$end_time = $end_time->format('Hi');
					}
				}
		    } 
		   
		    if($end_time == '0000'){
		    	$end_time = 2400;
		    }

	   		if((int)$start_time > (int)$end_time ) {
	   			// midnight situation
	   			$end_time = 2400 + (int)$end_time;
	   		}

	   		
		        // check if current time is within the range
		        if (((int)$start_time < (int)$currentTime) && ((int)$currentTime < (int)$end_time)) {
		            return TRUE;
		        }
		        
	        }
		} else {

			//backward compatibilty
			if(!empty($startTime) && is_numeric(substr($startTime, 0, 1)) ) {
				if(substr($startTime, -1)=='M'){
					$startTime = DateTime::createFromFormat('h:i A', $startTime)->format('Hi');			
				} else {
					$startTime = DateTime::createFromFormat('H:i', $startTime)->format('Hi');			
				}
				
		 	} 
		       //create time objects from start/end times and format as string (24hr AM/PM)
			if(!empty($endTime)  && is_numeric(substr($endTime, 0, 1))){
				if(substr($endTime, -1)=='M'){
					$endTime = DateTime::createFromFormat('h:i A', $endTime)->format('Hi');			
				} else {
					$endTime = DateTime::createFromFormat('H:i', $endTime)->format('Hi');
				}
		    } 
		    if($endTime == '0000'){
		    	$endTime = 2400;
		    }
		    
	   		if((int)$startTime > (int)$endTime ) {
	   			// midnight situation
	   			$endTime = 2400 + (int)$endTime;
	   		}
	   		
	        // check if current time is within the range
	        if (((int)$startTime < (int)$currentTime) && ((int)$currentTime < (int)$endTime)) {
	            return TRUE;
	        }
		}
		
    
	endif;
	
	if($status == false) {
		
		if(isset($storeSchedule[lcfirst(date( 'l', strtotime ( '-1 day' , $timestamp )))])) :

				$day = ($storeSchedule[lcfirst(date('l',(strtotime ( '-1 day' , $timestamp ) )))]);
				
				$startTime = $day['opens'];
				$endTime = $day['closes'];
				
				if(is_array($startTime)){
					foreach ($startTime as $key => $start_time) {
						
						# code...
						$end_time = $endTime[$key];
						//backward
						if(!empty($start_time) && is_numeric(substr($start_time, 0, 1)) ) {
							if(substr($start_time, -1)=='M'){
								$start_time = DateTime::createFromFormat('h:i A', $start_time)->format('Hi');			
							} else {
								$start_time = DateTime::createFromFormat('H:i', $start_time)->format('Hi');			
							}
							
					 	} 
					        //create time objects from start/end times and format as string (24hr AM/PM)
						if(!empty($end_time)  && is_numeric(substr($end_time, 0, 1))){
							if(substr($end_time, -1)=='M'){
								$end_time = DateTime::createFromFormat('h:i A', $end_time)->format('Hi');			
							} else {
								$end_time = DateTime::createFromFormat('H:i', $end_time)->format('Hi');
							}
					    } 

					  
						if( ((int)$start_time > (int)$end_time) && (int)$currentTime < (int)$end_time ) {
		 					return TRUE;

						}
					}

				} else {

					//backward
					if(!empty($startTime) && is_numeric(substr($startTime, 0, 1)) ) {
						if(substr($startTime, -1)=='M'){
							$startTime = DateTime::createFromFormat('h:i A', $startTime)->format('Hi');			
						} else {
							$startTime = DateTime::createFromFormat('H:i', $startTime)->format('Hi');			
						}
						
				 	} 
				        //create time objects from start/end times and format as string (24hr AM/PM)
					if(!empty($endTime)  && is_numeric(substr($endTime, 0, 1))){
						if(substr($endTime, -1)=='M'){
							$endTime = DateTime::createFromFormat('h:i A', $endTime)->format('Hi');			
						} else {
							$endTime = DateTime::createFromFormat('H:i', $endTime)->format('Hi');
						}
				    } 
					if( ((int)$startTime > (int)$endTime) && (int)$currentTime < (int)$endTime ) {
	 					$status = TRUE;

					}
				}
				
				
				
		endif;
		
	}
   	return $status;
    
}


function truelysell_get_timezone() {

    $tzstring = get_option( 'timezone_string' );
    $offset   = get_option( 'gmt_offset' );

    //Manual offset...
    //@see http://us.php.net/manual/en/timezones.others.php
    //@see https://bugs.php.net/bug.php?id=45543
    //@see https://bugs.php.net/bug.php?id=45528
    //IANA timezone database that provides PHP's timezone support uses POSIX (i.e. reversed) style signs
    if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
        $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
        $tzstring  = 'Etc/GMT'.$offset_st;
    }

    //Issue with the timezone selected, set to 'UTC'
    if( empty( $tzstring ) ){
        $tzstring = 'UTC';
    }

    $timezone = new DateTimeZone( $tzstring );
    return $timezone; 
}


function truelysell_check_if_has_hours(){
	$status = false;
	$has_hours = false;
	global $post;
	$days = truelysell_get_days();
	$storeSchedule = array();
	foreach ($days as $d_key => $value) {
		$open_val = get_post_meta($post->ID, '_'.$d_key.'_opening_hour', true);
		if(is_array($open_val)){
			
			if(!empty($open_val)){
				$has_hours = true;
			}

		} else {
			
			$opening = ($open_val) ? $open_val : '' ;	
			if(is_numeric(substr($opening, 0, 1))) {
				$has_hours = true;
			}

		}
	}
	
	return $has_hours;
}

function truelysell_get_geo_data($post){ 
	$terms = get_the_terms( $post->ID, 'listing_category' );
	
	if($terms ) {
		$term = array_pop($terms);	
		
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		$icon = get_term_meta($t_id,'icon',true);
		if($icon) {
			$icon = '<i class="'.$icon.'"></i>';	
		}	
	}
	if(is_tax('listing-category')){
		$term = get_queried_object();
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		$icon = get_term_meta($t_id,'icon',true);
		if($icon) {
			$icon = '<i class="'.$icon.'"></i>';	
		}	
	}
	if(isset($t_id)){
		$_icon_svg = get_term_meta($t_id,'_icon_svg',true);
		$_icon_svg_image = wp_get_attachment_image_src($_icon_svg,'medium');
	}
	if (isset($_icon_svg_image) && !empty($_icon_svg_image)) { 
    	$icon = truelysell_render_svg_icon($_icon_svg);
        //$icon = '<img class="truelysell-map-svg-icon" src="'.$_icon_svg_image[0].'"/>';

    
    } else { 

		if(empty($icon)){
			$icon = get_post_meta( $post->ID, '_icon', true );
		}
	
		if(empty($icon)){
			$icon = '<i class="sl sl-icon-location"></i>';
		}
	}

	$listing_type = get_post_meta( $post->ID, '_listing_type', true ); 
	
	$disable_address = truelysell_fl_framework_getoptions('disable_address');
	$latitude = get_post_meta( $post->ID, '_geolocation_lat', true ); 
	$longitude = get_post_meta( $post->ID, '_geolocation_long', true ); 
	if(!empty($latitude) && $disable_address) {
		$dither= '0.001';
		$latitude = $latitude + (rand(5,15)-0.5)*$dither;
	}

	$rating =esc_attr( get_post_meta($post->ID, 'truelysell-avg-rating', true ) ); 
	$reviews = truelysell_get_reviews_number($post->ID); 
	if(!$rating){
		$reviews = truelysell_get_google_reviews($post);
            if(!empty($reviews['result']['reviews'])){
                $rating = number_format_i18n($reviews['result']['rating'],1);
			
				$rating = str_replace(',', '.', $rating);
                $reviews = 5;
            }
            else {
				$reviews  = truelysell_get_reviews_number($post->ID); 
			}
	}
	ob_start(); ?>

	  	data-title="<?php the_title(); ?>"
	  	data-listing-type="<?php echo esc_attr($listing_type); ?>"
	  	data-classifieds-price="$<?php echo esc_attr(get_post_meta( $post->ID, '_classifieds_price', true )); ?>" 
    	data-friendly-address="<?php echo esc_attr(get_post_meta( $post->ID, '_friendly_address', true )); ?>" 
    	data-address="<?php the_listing_address(); ?>" 
    	data-image="<?php echo truelysell_core_get_listing_image( $post->ID ); ?>" 
    	data-longitude="<?php echo esc_attr( $latitude ); ?>" 
    	data-latitude="<?php echo esc_attr( $longitude ); ?>"
    	<?php if(!truelysell_fl_framework_getoptions('disable_reviews')){ ?>
    	data-rating="<?php echo $rating ?>"
    	data-reviews="<?php echo esc_attr( $reviews ); ?>"
    	<?php } ?>
    	data-icon="<?php echo esc_attr($icon); ?>"

    <?php 
    return ob_get_clean();
}

	function truelysell_get_unread_counter(){
        $user_id = get_current_user_id();
         global $wpdb;

        $result_1  = $wpdb -> get_var( "
        SELECT COUNT(*) FROM `" . $wpdb->prefix . "truelysell_core_conversations` 
        WHERE  user_1 = '$user_id' AND read_user_1 = 0
        ");
        $result_2  = $wpdb -> get_var( "
        SELECT COUNT(*) FROM `" . $wpdb->prefix . "truelysell_core_conversations` 
        WHERE  user_2 = '$user_id' AND read_user_2 = 0
        ");
        return $result_1+$result_2;
    }


    function truelysell_count_posts_by_user($post_author=null,$post_type=array(),$post_status=array()) {
	    global $wpdb;

	    if(empty($post_author))
	        return 0;

	    $post_status = (array) $post_status;
	    $post_type = (array) $post_type;

	    $sql = $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d AND ", $post_author );

	    //Post status
	    if(!empty($post_status)){
	        $argtype = array_fill(0, count($post_status), '%s');
	        $where = "(post_status=".implode( " OR post_status=", $argtype).') AND ';
	        $sql .= $wpdb->prepare($where,$post_status);
	    }

	    //Post type
	    if(!empty($post_type)){
	        $argtype = array_fill(0, count($post_type), '%s');
	        $where = "(post_type=".implode( " OR post_type=", $argtype).') AND ';
	        $sql .= $wpdb->prepare($where,$post_type);
	    }

	    $sql .='1=1';
	    $count = $wpdb->get_var($sql);
	    return $count;
	} 

	function truelysell_count_gallery_items( $post_id){
		if(!$post_id) { return; }

		$gallery = get_post_meta( $post_id, '_gallery', true );
		
		if(is_array($gallery)){
			return sizeof($gallery);	
		} else {
			return 0;
		}
		
	}

	function truelysell_get_reviews_number( $post_id = 0 ) {

	    global $wpdb, $post;

	    $post_id = $post_id ? $post_id : $post->ID;

	    return $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_parent = 0 AND comment_post_ID = $post_id AND comment_approved = 1" );

	}

	function truelysell_count_bookings($user_id,$status){
		global $wpdb;
		if( $status == 'approved' ) {
			$status_sql = "AND status IN ('confirmed','paid')";
		}
		else if ($status == 'waiting') {
			$status_sql = "AND status IN ('waiting','pay_to_confirm')";
		} else {
			$status_sql = "AND status='$status'";
		}
		
		$result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE owner_id=$user_id $status_sql", "ARRAY_A" );
		 return $wpdb->num_rows;
	}

	function truelysell_count_bookings_notification($user_id){
		global $wpdb;
		$status_sql = "AND rstatus='unread'";
		$result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE bookings_author=$user_id $status_sql", "ARRAY_A" );
		 return $wpdb->num_rows;
	}

	function truelysell_count_my_bookings($user_id){
		global $wpdb;
		$result  = $wpdb -> get_results( "SELECT * FROM `" . $wpdb->prefix . "bookings_calendar` WHERE NOT comment = 'owner reservations' AND (`bookings_author` = '$user_id') AND (`type` = 'reservation')", "ARRAY_A" );
		
		 return $wpdb->num_rows;
	}


if ( ! function_exists('truelysell_write_log')) {
   function truelysell_write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}



//cmb2 slots field
function cmb2_render_callback_for_slots( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	 $clock_format = truelysell_fl_framework_getoptions('clock_format') ?>

		<div class="availability-slots" data-clock-type="<?php echo esc_attr($clock_format); ?>hr">

		<?php 
		$days = array(
			'monday'	=> __('Monday','truelysell_core'),
			'tuesday' 	=> __('Tuesday','truelysell_core'),
			'wednesday' => __('Wednesday','truelysell_core'),
			'thursday' 	=> __('Thursday','truelysell_core'),
			'friday' 	=> __('Friday','truelysell_core'),
			'saturday' 	=> __('Saturday','truelysell_core'),
			'sunday' 	=> __('Sunday','truelysell_core'),
			); 
		
		if(!is_array($field->value)){
		$field = json_decode( $field->value );
		} else {
			$field = $field->value;
		}
		
		$int = 0;
		?>

		<?php foreach ($days as $id => $dayname) { 
			?>

			<!-- Single Day Slots -->
			<div class="day-slots">
				<div class="day-slot-headline">
					<?php echo esc_html($dayname); ?>
				</div>


				<!-- Slot For Cloning / Do NOT Remove-->
				<div class="single-slot cloned">
					<div class="single-slot-left">
						<div class="single-slot-time"><?php echo esc_html($dayname); ?></div>
						<button class="remove-slot"><i class="fa fa-close"></i></button>
					</div>

					<div class="single-slot-right">
						<strong><?php esc_html_e('Slots','truelysell_core'); ?></strong>
						<div class="plusminus horiz">
							<button></button>
							<input type="number" name="slot-qty" id="slot-qty" value="1" min="1" max="99">
							<button></button> 
						</div>
					</div>
				</div>		
				<!-- Slot For Cloning / Do NOT Remove-->

				<?php if (!isset( $field[$int][0]) ) { ?>
				<!-- No slots -->
				<div class="no-slots"><?php esc_html_e('No slots added','truelysell_core'); ?></div>
				<?php } ?>
				<!-- Slots Container -->
				<div class="slots-container">


			<!-- Slots from database loop -->
			<?php if ( isset( $field ) && is_array( $field[$int] ) ) foreach ( $field[$int] as $slot ) { // slots loop
					$slot = explode( '|', $slot);?>	
						<div class="single-slot ui-sortable-handle">
							<div class="single-slot-left">
								<div class="single-slot-time"><?php echo esc_html($slot[0]); ?></div>
								<button class="remove-slot"><i class="fa fa-close"></i></button>
							</div>

							<div class="single-slot-right">
								<strong><?php esc_html_e('Slots','truelysell_core'); ?></strong>
								<div class="plusminus horiz">
									<button disabled=""></button>
									<input type="number" name="slot-qty" id="slot-qty" value="<?php echo esc_html($slot[1]); ?>" min="1" max="99">
									<button></button> 
								</div>
							</div>
						</div>
				<?php } ?>			
				<!-- Slots from database / End -->		

				</div>
				<!-- Slots Container / End -->
				<!-- Add Slot -->
				<div class="add-slot">
					<div class="add-slot-inputs">
						<input type="time" class="time-slot-start" min="00:00" max="12:59"/>
						<?php if( $clock_format == '12'){ ?>
						<select class="time-slot-start twelve-hr" id="">
							<option><?php esc_html_e('am','truelysell_core'); ?></option>
							<option><?php esc_html_e('pm','truelysell_core'); ?></option>
						</select>
						<?php } ?>

						<span>-</span>

						<input type="time" class="time-slot-end" min="00:00" max="12:59"/>
						<?php if( $clock_format == '12'){ ?>
						<select class="time-slot-end twelve-hr" id="">
							<option><?php esc_html_e('am'); ?></option>
							<option><?php esc_html_e('pm'); ?></option>
						</select>
						<?php } ?>

					</div>
					<div class="add-slot-btn">
						<button><?php esc_html_e('Add','truelysell_core'); ?></button>
					</div>
				</div>
			</div>
		<?php 
		$int++;
		} ?>

		</div>
	
	<?php 
	echo $field_type_object->input( array( 'type' => 'hidden' ) );
}
add_action( 'cmb2_render_slots', 'cmb2_render_callback_for_slots', 10, 5 );

function cmb2_render_callback_for_truelysell_calendar( $field, $escaped_value, $object_id, $object_type, $field_type){

	$calendar = new Truelysell_Core_Calendar;

	echo $calendar->getCalendarHTML();
	// make sure we specify each part of the value we need.
	$value = wp_parse_args( $field->value, array(
		
		'dates'     => '',
		'price'       => '',
	) );

	echo $field_type->input( array(
			'name'  => $field_type->_name( '[dates]' ),
			'id'    => $field_type->_id( 'dates' ),
			'class'    => 'truelysell-calendar-avail',
			'value' => esc_attr($value['dates']),
			'type'  => 'hidden',
		) );
	echo $field_type->input( array(
			'name'  => $field_type->_name( '[price]' ),
			'id'    => $field_type->_id( 'price' ),
			'class'    => 'truelysell-calendar-price',
			'value' => esc_attr($value['price']),
			'type'  => 'hidden',
		) ); ?>

<?php
}
add_action( 'cmb2_render_truelysell_calendar', 'cmb2_render_callback_for_truelysell_calendar', 10, 5 );

function truelysell_get_bookable_services($post_id){

	$services = array();
	
	$_menu = get_post_meta( $post_id, '_menu', 1 );
	if($_menu) {
		foreach ($_menu as $menu) { 
		
			if(isset($menu['menu_elements']) && !empty($menu['menu_elements'])) :
				foreach ($menu['menu_elements'] as $item) {
					if(isset($item['bookable'])){

						$services[] = $item;	
					}
				}
			endif;
	
		}
	}
	
	return $services;
}



/**
 * Prepares files for upload by standardizing them into an array. This adds support for multiple file upload fields.
 *
 * @since 1.21.0
 * @param  array $file_data
 * @return array
 */
function truelysell_prepare_uploaded_files( $file_data ) {
	$files_to_upload = array();

	if ( is_array( $file_data['name'] ) ) {
		foreach( $file_data['name'] as $file_data_key => $file_data_value ) {
			if ( $file_data['name'][ $file_data_key ] ) {
				$type              = wp_check_filetype( $file_data['name'][ $file_data_key ] ); // Map mime type to one WordPress recognises
				$files_to_upload[] = array(
					'name'     => $file_data['name'][ $file_data_key ],
					'type'     => $type['type'],
					'tmp_name' => $file_data['tmp_name'][ $file_data_key ],
					'error'    => $file_data['error'][ $file_data_key ],
					'size'     => $file_data['size'][ $file_data_key ]
				);
			}
		}
	} else {
		$type              = wp_check_filetype( $file_data['name'] ); // Map mime type to one WordPress recognises
		$file_data['type'] = $type['type'];
		$files_to_upload[] = $file_data;
	}

	return apply_filters( 'truelysell_prepare_uploaded_files', $files_to_upload );
}



/**
 * Uploads a file using WordPress file API.
 *
 * @since 1.21.0
 * @param  array|WP_Error      $file Array of $_FILE data to upload.
 * @param  string|array|object $args Optional arguments
 * @return stdClass|WP_Error Object containing file information, or error
 */
function truelysell_upload_file( $file, $args = array() ) {
	global $truelysell_upload, $truelysell_uploading_file;

	include_once( ABSPATH . 'wp-admin/includes/file.php' );
	include_once( ABSPATH . 'wp-admin/includes/media.php' );

	$args = wp_parse_args( $args, array(
		'file_key'           => '',
		'file_label'         => '',
		'allowed_mime_types' => '',
	) );

	$truelysell_upload         = true;
	$truelysell_uploading_file = $args['file_key'];
	$uploaded_file              = new stdClass();
	
	$allowed_mime_types = $args['allowed_mime_types'];
	

	/**
	 * Filter file configuration before upload
	 *
	 * This filter can be used to modify the file arguments before being uploaded, or return a WP_Error
	 * object to prevent the file from being uploaded, and return the error.
	 *
	 * @since 1.25.2
	 *
	 * @param array $file               Array of $_FILE data to upload.
	 * @param array $args               Optional file arguments
	 * @param array $allowed_mime_types Array of allowed mime types from field config or defaults
	 */
	$file = apply_filters( 'truelysell_upload_file_pre_upload', $file, $args, $allowed_mime_types );

	if ( is_wp_error( $file ) ) {
		return $file;
	}

	if ( ! in_array( $file['type'], $allowed_mime_types ) ) {
		if ( $args['file_label'] ) {
			return new WP_Error( 'upload', sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s', 'truelysell_core' ), $args['file_label'], $file['type'], implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		} else {
			return new WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', 'truelysell_core' ), implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		}
	} else {
		$upload = wp_handle_upload( $file, apply_filters( 'submit_property_wp_handle_upload_overrides', array( 'test_form' => false ) ) );
		if ( ! empty( $upload['error'] ) ) {
			return new WP_Error( 'upload', $upload['error'] );
		} else {
			$uploaded_file->url       = $upload['url'];
			$uploaded_file->file      = $upload['file'];
			$uploaded_file->name      = basename( $upload['file'] );
			$uploaded_file->type      = $upload['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->extension = substr( strrchr( $uploaded_file->name, '.' ), 1 );
		}
	}

	$truelysell_upload         = false;
	$truelysell_uploading_file = '';

	return $uploaded_file;
}



/**
 * Returns mime types specifically for WPJM.
 *
 * @since 1.25.1
 * @param   string $field Field used.
 * @return  array  Array of allowed mime types
 */
function truelysell_get_allowed_mime_types( $field = '' ){
	
		$allowed_mime_types = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'pdf'          => 'application/pdf',
			'doc'          => 'application/msword',
			'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		);
	
	/**
	 * Mime types to accept in uploaded files.
	 *
	 * Default is image, pdf, and doc(x) files.
	 *
	 * @since 1.25.1
	 *
	 * @param array  {
	 *     Array of allowed file extensions and mime types.
	 *     Key is pipe-separated file extensions. Value is mime type.
	 * }
	 * @param string $field The field key for the upload.
	 */
	return apply_filters( 'truelysell_mime_types', $allowed_mime_types, $field );
}


//truelysell_fields_for_cmb2


if(!function_exists('truelysell_date_to_cal')) {
    function truelysell_date_to_cal($timestamp) {
      return date('Ymd\THis\Z', $timestamp);
    }
}

if(!function_exists('truelysell_escape_string')) {
    function truelysell_escape_string($string) {
      return preg_replace('/([\,;])/','\\\$1', $string);
    }
}

function truelysell_calculate_service_price($service, $guests, $days, $countable ){

	if(isset($service['bookable_options'])) {
		switch ($service['bookable_options']) {
			case 'onetime':
				$price = $service['price'];
				break;
			case 'byguest':
				$price = $service['price'] * (int) $guests;
				
				break;
			case 'bydays':
				$price = $service['price'] * (int) $days;
				break;
			case 'byguestanddays':
				$price = $service['price'] * (int) $days * (int) $guests;
				break;
			default:
				$price = $service['price'];
				break;
		}
		return $price * (int)$countable;
	} else {
		return $service['price'] * (int)$countable;
	}
}

function truelysell_get_extra_services_html($arr) {
	$output = '';
	if(is_array($arr)){
		$output .= '<ul class="truelysell_booked_services_list">';
			$currency_abbr = truelysell_fl_framework_getoptions('currency' );
			$currency_postion = truelysell_fl_framework_getoptions('currency_postion' );
			$currency_symbol = Truelysell_Core_Listing::get_currency_symbol($currency_abbr);

		foreach ($arr as $key => $booked_service) {
			
			$price = esc_html__('Free','truelysell_core');
			if(isset($booked_service->price)){
				if($booked_service->price == 0) {
					$price = esc_html__('Free','truelysell_core');
				} else {
					$price = '';
					if($currency_postion == 'before') { $price .= $currency_symbol.' '; }  
					$price .= $booked_service->price;		
					if($currency_postion == 'after') { $price .= ' '.$currency_symbol; }  
					
				}

			}
			
			$output .= '<li>'.$booked_service->service->name;
			if(isset($booked_service->countable) && $booked_service->countable > 1){
				$output .= 	' <em>(*'.$booked_service->countable.')</em>';
			}
			
			$output .=  '<span class="services-list-price-tag">'.$price.'</span></li>';
			
			# code...
		}
		$output .= '</ul>';
		return $output;
	} else {
		return wpautop( $arr );
	}
}

function truelysell_get_users_name( $user_id = null ) {

	$user_info = $user_id ? new WP_User( $user_id ) : wp_get_current_user();

	if ( $user_info->first_name ) {

	 	if ( $user_info->last_name ) {
			 return $user_info->first_name . ' ' . $user_info->last_name;
	 	}

	 	return $user_info->first_name;
	 }

	 return $user_info->display_name;
}

function truelysell_get_extra_registration_fields($role){
	if($role == 'owner'){
		$fields = truelysell_fl_framework_getoptions('owner_registration_form');
	} else {
		$fields = truelysell_fl_framework_getoptions('guest_registration_form');
	}
	if(!empty($fields)){

		ob_start();
		?>
		<div id="truelysell-core-registration-<?php echo esc_attr($role);?>-fields">
			<?php
			foreach ( $fields as $key => $field ) : 
			
				if($field['type'] == 'header'){ ?>
					<h4 class="truelysell_core-registration-separator"><?php esc_html_e($field['placeholder']) ?></h4>
				<?php }
				$field['value'] = false;
		

				$template_loader = new Truelysell_Core_Template_Loader;

				// fix the name/id mistmatch
				if(isset($field['id'])){
					$field['name'] = $field['id'];
				}
				$field['form_type'] = 'registration';
			
				if($field['type']=='select_multiple') {
					
						$field['type'] = 'select';
						$field['multi'] = 'on';
						$field['placeholder'] = '';
				}	
				if($field['type']=='multicheck_split') {
				
					$field['type'] = 'checkboxes';
				}
				if($field['type']=='wp-editor') {
					$field['type'] = 'textarea';
				}
 				

 					$has_icon = false;
					if( !in_array($field['type'],array('checkbox','select','select_multiple')) && isset($field['icon']) && $field['icon'] != 'empty') {
						$has_icon = true;
					}
					?>	
					<div class="form-group">	
					

					<?php 
					 
									 if($field['type'] != 'hidden'): ?>
 										<label class="col-form-label label-<?php echo esc_attr( $key ); ?>" for="<?php echo esc_attr( $key ); ?>">
											<?php echo $field['placeholder'];?>
											 
										</label>
					 <?php endif; ?>
 					<div  class="<?php if(!$has_icon) { echo "field-no-icon"; } ?> truelysell-registration-custom-<?php echo esc_attr($field['type']); ?>" id="truelysell-registration-custom-<?php echo esc_attr($key); ?>" for="<?php echo esc_attr($key); ?>">
						
						<?php 
						
						if($has_icon) { ?>

							<i class="<?php echo esc_attr($field['icon']); ?>"></i><?php 
						}

						$template_loader->set_template_data( array( 'key' => $key, 'field' => $field,	) )->get_template_part( 'form-fields/' . $field['type'] );
						$has_icon = false;
					?>	

					</div>
					</div>
				<?php 
					
			endforeach; ?>
		</div>
		<?php  return ob_get_clean(); 
	} else {
		return false;
	}

}


function workscout_b472b0_admin_notice(){
        
        $activation_date = get_option('truelysell_activation_date');
        
        $db_option = get_option('truelysell_core_db_version');
       

       if(empty($activation_date)){
           	if ( $db_option && version_compare( $db_option, '1.5.18', '<=' ) ) {
				update_option('truelysell_activation_date',time());
				$activation_date = time();
				update_option( 'truelysell_core_db_version', '1.5.19' );
			}
       }
       $current_time = time();
       $time_diff = ($current_time-$activation_date)/86400;

       if($time_diff>4){


    
        $licenseKey   = get_option("Truelysell_lic_Key","");
        $liceEmail    = get_option( "Truelysell_lic_email","");
            
        $templateDir  = get_template_directory(); //or dirname(__FILE__);
    	
    	$show_message = false;

        // if(empty($licenseKey) && TruelysellBase::CheckWPPlugin( $licenseKey, $liceEmail, $licenseMessage, $responseObj, $templateDir."/style.css")){  

        // ob_start();

            ?>
                <!-- <div class="license-validation-popup license-nulled">
                    <p>Hi, it seems you are using unlicensed version of Truelysell!</p>
                    <ul>
                      <li>Nulled software may contain malware.</li>
                      <li>Malicious code can steal informations from your website.</li>
                      <li>A nulled version can add spammy links and malicious redirects to your websites. Search engines penalize this kind of activity.</li>
                      <li>Denied udpates. You can't update a nulled Truelysell.</li>
                      <li>No Support. You won't get support from us if you run in any problems with your site. And <a class="link" href="https://themeforest.net/item/truelysell-directory-listings-wordpress-theme/reviews/23239259?utf8=%E2%9C%93&reviews_controls%5Bsort%5D=ratings_descending">our Support is awesome</a>.</li>
                      <li>Legal issues. Nulled plugins may involve the distribtuion of illegal material or data theft, leading to legal proceedings</li>
                    </ul>
                    <a style="zoom:1.3" href="https://bit.ly/truelysell-nulled" class="nav-tab">Buy Legal License (One time Payment) &#8594;</a><br>
                    <small>Buy legal version and get clean and tested code directly from the developer, your purchase will support ongoing improvements of Truelysell</small>
                </div> -->
            
            <?php //$html = ob_get_clean();
            //echo $html;

        // }
      }          

}
add_action('admin_notices', 'workscout_b472b0_admin_notice');



function truelysell_get_term_post_count( $taxonomy = 'category', $term = '', $args = [] )
{
    // Lets first validate and sanitize our parameters, on failure, just return false
    if ( !$term )
        return false;

    if ( $term !== 'all' ) {
        if ( !is_array( $term ) ) {
            $term = filter_var(       $term, FILTER_VALIDATE_INT );
        } else {
            $term = filter_var_array( $term, FILTER_VALIDATE_INT );
        }
    }

    if ( $taxonomy !== 'category' ) {
        $taxonomy = filter_var( $taxonomy, FILTER_SANITIZE_STRING );
        if ( !taxonomy_exists( $taxonomy ) )
            return false;
    }

    if ( $args ) {
        if ( !is_array ) 
            return false;
    }

    // Now that we have come this far, lets continue and wrap it up
    // Set our default args
    $defaults = [
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'post_status' => 'publish',
        'post_type' => array('listing')
    ];

    if ( $term !== 'all' ) {
        $defaults['tax_query'] = [
            [
                'taxonomy' => $taxonomy,
                'terms'    => $term
            ]
        ];
    }

    $combined_args = wp_parse_args( $args, $defaults );
    $q = new WP_Query( $combined_args );

    // Return the post count
    return $q->found_posts;
}


if ( ! function_exists( 'dokan_store_category_menu' ) ) :

    /**
     * Store category menu for a store
     *
     * @param  int $seller_id
     *
     * @since 3.2.11 rewritten whole function
     *
     * @return void
     */
    function dokan_store_category_menu( $seller_id, $title = '' ) {
        ?>
    <div id="cat-drop-stack" class="store-cat-stack-dokan">
        <?php
        $seller_id = empty( $seller_id ) ? get_query_var( 'author' ) : $seller_id;
        $vendor    = dokan()->vendor->get( $seller_id );
        if ( $vendor instanceof \WeDevs\Dokan\Vendor\Vendor ) {
            $categories = $vendor->get_store_categories();
            $walker = new \WeDevs\Dokan\Walkers\StoreCategory( $seller_id );
            echo '<ul>';
            echo call_user_func_array( array( &$walker, 'walk' ), array( $categories, 0, array() ) ); //phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
            echo '</ul>';
        }
        ?>
    </div>
        <?php
    }

endif;