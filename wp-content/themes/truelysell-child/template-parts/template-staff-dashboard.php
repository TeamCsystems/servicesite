<?php

/**
 * Template Name: Staff Dashboard Page
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Truelysell
 */
get_header();
    session_start();


// Check if the user ID is set in the session
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
    // You can use $user_id as needed

//  print_r($current_user_id);
//  die;

global $wpdb;
$table_name = $wpdb->prefix . 'staffs'; 

// Query the database for data associated with the current user
$query = $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $current_user_id);
$results = $wpdb->get_results( $query );
foreach($results as $result ){
    $resultArray = (array) $result;
}
if(empty($query)){

    wp_redirect(home_url('/staff-login-page'));
}
else { //is logged

	// get_header('dashboard');
   $staff_id = $resultArray['id'];
  $current_business_id = $resultArray['current_userid'];
    $staff_email = $resultArray['staff_email'];
    $staff_name = $resultArray['staff_name'];
    $staff_role = $resultArray['role'];
	$staff_status = $resultArray['status'];

?>
	<!-- Dashboard -->
	<div class="content">
	 
		<div class="container">
		<?php
$login_success_message = get_transient('login_success_message');
if ($login_success_message) {
    echo '<div class="success-message" style="color: green;">' . esc_html($login_success_message) . '</div>';
    delete_transient('login_success_message'); 
}
?>		<div class="row">
			<div class="col-md-4 col-lg-3 theiaStickySidebar dashboard-nav">
				<div class="settings-widget">
					<div class="settings-header">
						<div class="settings-img">
						<img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2024/02/profile.png" width="85px"/>
						</div>
						<h6><?php echo esc_html($staff_name);?></h6>
					</div>
 
				<div class="widget settings-menu sidebar-menu" id="sidebar-menu">
					<ul >
					
					<?php
					
						if (($staff_role == 'staff')) : ?>
							
						<li class="active"><a  href="#"><i class="feather-book"></i> <?php esc_html_e('My Bookings', 'truelysell'); ?></a></li>
							
					<?php endif; ?>
					
					<!-- Logout -->
					<li>
					    <a href="<?php echo esc_url( home_url( '/staff-login-page' ) ) . '?id=' . $staff_id . '&action=' . esc_url( home_url( '/staff-dashboard' ) ); ?>">
						<i class="feather-log-out"></i> <?php esc_html_e('Logout', 'truelysell'); ?>
					    </a>
					</li>



				</ul>
				</div>
		
				
				
			
			</div>
			
			<!-- Content
			================================================== -->
			
			
			
		</div>
		<div class="col-md-8 col-lg-9 dashboard-nav">
				<div id="main">
    					<!--<div class="fof">
        					<h2>No Bookings</h2>
        					
    					</div>
    					</br>
    					<img src="<?php //echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2024/02/bookshelf-1.png" width="85px"/>-->
    					
    					
    					<!--Starting Boooking Section-->
    	<?php
    	global $wpdb;
	$table_b = $wpdb->prefix . 'bookings_calendar';

	$query = $wpdb->prepare("SELECT * FROM $table_b WHERE staff_id = %d", $staff_id);
	$result = $wpdb->get_results( $query );
	if (!empty($result)) {
	foreach($result as $datas ){
	   $data = (array) $datas;
	    $booking_data = json_decode($data['comment']);
	    
	    
	 
	?>
	<div class="booking-list " id="booking-list">
	 <div class="booking-widget">
		<?php if (has_post_thumbnail($data['listing_id'] ) ): ?>
			<div class="booking-img">
			<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $data['listing_id'] ), 'single-post-thumbnail' ); ?>
			<a href="<?php echo get_permalink($data['listing_id']); ?>" class="booking-img"><img src="<?php echo $image[0]; ?>" alt="User Image"></a>
			</div>
			<?php else : ?>
			<div class="booking-img">
			<img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2024/02/profile.png" >
			</div>
			<?php endif; ?>
		<div class="booking-det-info">
			<h3 id="title"><a href="<?php echo get_permalink($data['listing_id']); ?>"><?php echo get_the_title($data['listing_id']); ?></a></h3>
			<ul class="booking-details">
				<li>
				<span class="book-item"><?php esc_html_e('Booking Date', 'truelysell_core'); ?></span> :
								
								<?php echo date_i18n(get_option( 'date_format' ), strtotime($data['date_start'])); ?> 
								<?php 
									 $time_start = date_i18n(get_option( 'time_format' ), strtotime($data['date_start']));
									$time_end = date_i18n(get_option( 'time_format' ), strtotime($data['date_end']));
									?>

						
						
						 
				</li>
				<li>
					<span class="book-item"><?php esc_html_e('Booking Time', 'truelysell_core'); ?></span> :
					
									
									<?php 
										 $time_start = date_i18n(get_option( 'time_format' ), strtotime($data['date_start']));
										 $time_end = date_i18n(get_option( 'time_format' ), strtotime($data['date_end']));
										?>

									<?php echo $time_start ?> <?php if($time_start != $time_end) echo '- '.$time_end; ?>
						
						
				</li>
				<li>
					<span class="book-item"><?php esc_html_e('Amount', 'truelysell_core'); ?></span> :
					<?php
					 $currency_abbr = truelysell_fl_framework_getoptions('currency' );
					$currency_postion = truelysell_fl_framework_getoptions('currency_postion' );
					 $currency_symbol = Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
					$decimals = truelysell_fl_framework_getoptions('number_decimals');

					if($booking_data->price): ?>
								
								<?php  echo $currency_symbol ?><?php 	
								if(is_numeric($booking_data->price)){
									echo number_format_i18n($booking_data->price,$decimals);
								} else {
									echo esc_html($booking_data->price);
								}; ?>
								
					<?php endif; ?>	
				</li>

<?php  

 if (isset($booking_data->billing_address_1) && !empty($booking_data->billing_address_1)) {  ?>
<li><span  class="book-item"><?php esc_html_e('Location', 'truelysell_core'); ?></span> : 

  		 <?php if(isset($booking_data->billing_address_1)) echo esc_html(stripslashes($booking_data->billing_address_1)).','; ?> 
		 <?php if(isset($booking_data->billing_city)) echo esc_html(stripslashes($booking_data->billing_city)).','; ?>
		 <?php if(isset($booking_data->billing_country)) echo esc_html(stripslashes($booking_data->billing_country)).','; ?>
		 <?php if(isset($booking_data->billing_postcode)) echo esc_html(stripslashes($booking_data->billing_postcode)); ?>
</li>
 <?php } else { ?>

	<?php $address = get_post_meta( $data->listing_id, '_address', true ); 
					if($address) {  ?>
				<li>
					<?php $address = get_post_meta( $data->listing_id, '_address', true );  ?>
					
						<span class="book-item"><?php esc_html_e('Location', 'truelysell_core'); ?></span> :
							<?php echo esc_html($address); ?>
					
				</li>
				<?php } } ?>

				
				<li>
					 
					<span class="book-item"><?php esc_html_e('Customer', 'truelysell_core'); ?></span> :
					<div class="user-book">
					<div class="avatar avatar-xs">
						<?php echo get_avatar($data->bookings_author, '26', '', '', array('class' => 'avatar-img rounded-circle')) ?>
					</div>
					<?php 
					if( isset($booking_data->first_name) || isset($booking_data->last_name) ) : ?>
							<?php if(isset($booking_data->first_name)) echo esc_html(stripslashes($booking_data->first_name)); ?> <?php 
							    if(isset($booking_data->last_name)) echo esc_html(stripslashes($booking_data->last_name)); ?>
					<?php endif; ?>
					</div>
				       <p><?php echo esc_html($booking_data->email); ?> </p><?php 
 					if( isset($booking_data->phone)) : ?>
						<p><?php echo esc_html($booking_data->phone); ?> </p>
					<?php endif; ?> 
                       

					   
					   

				</li>
				

			</ul>
		</div>
	</div>
</div>
<?php }
}
else{
?><div class="fof">
        					<h2>No Bookings</h2>
        					
    					</div>
    					</br>
    					<img src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/uploads/2024/02/bookshelf-1.png" width="85px"/>
<?php }?>
			
			<!--End-->	
				</div>
			
		</div>
		
	      </div>
		<!-- Navigation / End -->
	  </div>
	</div>
	<!-- Dashboard / End -->
<?php
}
	get_footer();
}
else{
    wp_redirect(home_url('/staff-login-page'));
}
?>

