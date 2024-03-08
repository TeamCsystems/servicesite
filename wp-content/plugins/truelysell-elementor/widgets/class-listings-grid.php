<?php
/**
 * Awesomesauce class.
 *
 * @category   Class
 * @package    ElementorAwesomesauce
 * @subpackage WordPress
 * @author     Ben Marshall <me@benmarshall.me>
 * @copyright  2020 Ben Marshall
 * @license    https://opensource.org/licenses/GPL-3.0 GPL-3.0-only
 * @link       link(https://www.benmarshall.me/build-custom-elementor-widgets/,
 *             Build Custom Elementor Widgets)
 * @since      1.0.0
 * php version 7.3.9
 */

namespace ElementorTruelysell\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Awesomesauce widget class.
 *
 * @since 1.0.0
 */
class ListingsGrid extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'truelysell-listings-grid';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Truelysell Listings Grid', 'truelysell_elementor' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'truelysell' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
 


	$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'truelysell_elementor' ),
			)
		);

		$this->add_control(
			'limit',
			[
				'label' => __( 'Listings to display', 'truelysell_elementor' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 21,
				'step' => 1,
				'default' => 6,
			]
		);


		$this->add_control(
			'orderby',
			[
				'label' => __( 'Order by', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'none' =>  __( 'No order', 'truelysell_elementor' ),
					'ID' =>  __(  'Order by post id. ', 'truelysell_elementor' ),
					'author'=>  __(  'Order by author.', 'truelysell_elementor' ),
					'title' =>  __(  'Order by title.', 'truelysell_elementor' ),
					'name' =>  __( ' Order by post name (post slug).', 'truelysell_elementor' ),
					'type'=>  __( ' Order by post type.', 'truelysell_elementor' ),
					'date' =>  __( ' Order by date.', 'truelysell_elementor' ),
					'modified' =>  __( ' Order by last modified date.', 'truelysell_elementor' ),
					'parent' =>  __( ' Order by post/page parent id.', 'truelysell_elementor' ),
					'rand' =>  __( ' Random order.', 'truelysell_elementor' ),
					'comment_count' =>  __( ' Order by number of commen', 'truelysell_elementor' ),
					
				],
			]
		);
		$this->add_control(
			'order',
			[
				'label' => __( 'Order', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC' =>  __( 'Descending', 'truelysell_elementor' ),
					'ASC' =>  __(  'Ascending. ', 'truelysell_elementor' ),
				
					
				],
			]
		);


		$this->add_control(
			'_listing_type',
			[
				'label' => __(
					'Show only Listing Types',
					'truelysell_elementor'
				),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'default' => '',
				'options' => [
					'' =>  __('All', 'truelysell_elementor'),
					'service' =>  __('Service', 'truelysell_elementor'),
 				],
			]
		);

			$this->add_control(
				'tax-listing_category',
				[
					'label' => __( 'Show only from listing categories', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('listing_category'),
					
				]
			);				

			$this->add_control(
				'tax-service_category',
				[
					'label' => __( 'Show only from service categories', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('service_category'),
					
				]
			);	

			$this->add_control(
				'tax-rental_category',
				[
					'label' => __( 'Show only from rental categories', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('rental_category'),
					
				]
			);				

			$this->add_control(
				'tax-event_category',
				[
					'label' => __( 'Show only from event categories', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('event_category'),
					
				]
			);
			$this->add_control(
				'tax-classifieds_category',
				[
					'label' => __('Show only from classifieds categories', 'truelysell_elementor'),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('classifieds_category'),

				]
			);	

			$this->add_control(
				'exclude_posts',
				[
					'label' => __( 'Exclude listings', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_posts(),
					
				]
			);	
			$this->add_control(
				'include_posts',
				[
					'label' => __( 'Include listings', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_posts(),
					
				]
			);

			

			$this->add_control(
				'feature',
				[
					'label' => __( 'Show only listings with features', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('listing_feature'),
				]
			);

			$this->add_control(
				'region',
				[
					'label' => __( 'Show only listings from region', 'truelysell_elementor' ),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'default' => [],
					'options' => $this->get_terms('region'),
				]
			);	


			$this->add_control(
			'relation',
			[
				'label' => __( 'Taxonomy Relation', 'truelysell_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'OR' =>  __( 'OR (listings in one of selected taxonomies)', 'truelysell_elementor' ),
					'AND' =>  __(  'AND  (listings in all of selected taxonomies) ', 'truelysell_elementor' ),
				
					
				],
			]
			);

					$this->add_control(
				'featured',
				[
					'label' => __( 'Show only featured listings', 'truelysell_elementor' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label_on' => __( 'Show', 'your-plugin' ),
					'label_off' => __( 'Hide', 'your-plugin' ),
					'return_value' => 'yes',
					'default' => 'no',
				]
			);

	$this->end_controls_section();
$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Settings', 'truelysell_elementor' ),
			)
		);

			$this->add_control(
			'style',
			[
				'label' => __( 'Style', 'truelysell_elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1' =>  __( 'Style 1', 'truelysell_elementor' ),
					'style2' =>  __( 'Style 2', 'truelysell_elementor' ),
					'style3' =>  __( 'Style 3', 'truelysell_elementor' ),
					'style4' =>  __( 'Style 4', 'truelysell_elementor' ),
					'style5' =>  __( 'Style 5', 'truelysell_elementor' ),
					'style6' =>  __( 'Style 6', 'truelysell_elementor' ),
				],
			]
		);
		$this->add_control(
			'link',
			array(
				'label'   => __( 'Link', 'truelysell_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'truelysell_elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'View All',
			)
		);
		
  
		$this->end_controls_section();

		
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
	 
		$settings = $this->get_settings_for_display();


		$limit = $settings['limit'] ? $settings['limit'] : 3;
		$orderby = $settings['orderby'] ? $settings['orderby'] : 'title';
		$order = $settings['order'] ? $settings['order'] : 'ASC';
		$exclude_posts = $settings['exclude_posts'] ? $settings['exclude_posts'] : array();
		$include_posts = $settings['include_posts'] ? $settings['include_posts'] : array();
		
		
	//var_dump($settings);

		$output = '';
        $randID = rand(1, 99); // Get unique ID for carousel

        $meta_query = array();

       
        $args = array(
            'post_type' => 'listing',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'orderby' => $orderby,
            'order' => $order,
            'tax_query'              => array(),
            'meta_query'              => array(),
            );

        if(isset($settings['featured']) && $settings['featured'] == 'yes'){
            $args['meta_key'] = '_featured';
            $args['meta_value'] = 'on';
 
        }
 
        if(!empty($exclude_posts)) {
            $exl = is_array( $exclude_posts ) ? $exclude_posts : array_filter( array_map( 'trim', explode( ',', $exclude_posts ) ) );
            $args['post__not_in'] = $exl;
        }

        if(!empty($include_posts)) {
            $inc = is_array( $include_posts ) ? $include_posts : array_filter( array_map( 'trim', explode( ',', $include_posts ) ) );
            $args['post__in'] = $inc;
        }

        if($settings['feature']){
            $feature = is_array( $settings['feature'] ) ? $settings['feature'] : array_filter( array_map( 'trim', explode( ',', $settings['feature'] ) ) );
            foreach ($feature as $key) {
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'listing_feature',
                   'field'    =>   'slug',
                   'terms'    =>   $key,
                   
                ));
            }
        }

        if(isset($settings['tax-listing_category'])){
            $category = is_array( $settings['tax-listing_category'] ) ? $settings['tax-listing_category'] : array_filter( array_map( 'trim', explode( ',', $settings['tax-listing_category'] ) ) );
            
            foreach ($category as $key) {
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'listing_category',
                   'field'    =>   'slug',
                   'terms'    =>   $key,
                   
                ));
            }
        }

        if(isset($settings['tax-service_category'])){
            $category = is_array( $settings['tax-service_category'] ) ? $settings['tax-service_category'] : array_filter( array_map( 'trim', explode( ',', $settings['tax-service_category'] ) ) );
            foreach ($category as $key) {
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'service_category',
                   'field'    =>   'slug',
                   'terms'    =>   $key,
                   
                ));
            }
        }
        if(isset($settings['tax-rental_category'])){
            $category = is_array( $settings['tax-rental_category'] ) ? $settings['tax-rental_category'] : array_filter( array_map( 'trim', explode( ',', $settings['tax-rental_category'] ) ) );
            foreach ($category as $key) {
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'rental_category',
                   'field'    =>   'slug',
                   'terms'    =>   $key,
                   
                ));
            }
        }

        if(isset($settings['tax-event_category'])){
            $category = is_array( $settings['tax-event_category'] ) ? $settings['tax-event_category'] : array_filter( array_map( 'trim', explode( ',', $settings['tax-event_category'] ) ) );
            foreach ($category as $key) {
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'event_category',
                   'field'    =>   'slug',
                   'terms'    =>   $key,
                   
                ));
            }
        }

        if($settings['region']){
            
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'region',
                   'field'    =>   'slug',
                   'terms'    =>   $settings['region'],
                   'operator' =>   'IN'
                   
                ));
            
        }
         $args['tax_query']['relation'] =  $settings['relation'];

		if ($settings['_listing_type']) {
			array_push($args['meta_query'], array(
				'key'     => '_listing_type',
				'value'   => $settings['_listing_type'],
				'compare' => '='

			));
		}
		

        if(!empty($tags)) {
            $tags         = is_array( $tags ) ? $tags : array_filter( array_map( 'trim', explode( ',', $tags ) ) );
            $args['tag__in'] = $tags;
        }
       
        
        $i = 0;

        $wp_query = new \WP_Query( $args );
        if(!class_exists('Truelysell_Core_Template_Loader')) {
            return;
        }
        $template_loader = new \Truelysell_Core_Template_Loader;

        ob_start(); ?>
 <?php if($settings['style']=="style1") {  ?>

	<div class="row">
         <?php 
            if ( $wp_query->have_posts() ) {
               
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					  $idd = $wp_query->post->ID;
					  $is_featured = truelysell_core_is_featured($idd); 
					  ?>
<div class="col-lg-4 col-md-6 col-sm-6 col-12">
<div class="service-widget aos" data-aos="fade-up">
  				 <div class="service-img">
                <?php $template_loader->get_template_part('content-listing-image-small');  
                $rating_value = get_post_meta($idd, 'truelysell-avg-rating', true);
                ?>
  		 
		   <div class="item-info">
 			   <div class="cate-list">
			   <?php 
				   $terms = get_the_terms($idd, 'listing_category' ); 
				   if ( $terms && ! is_wp_error( $terms ) ) :  
					   $main_term = array_pop($terms); ?>
					   <span class="cat_name bg-yellow"><?php echo $main_term->name; ?></span>
				   <?php endif; ?>
			   
			   </div>
 			   <div class="fav-item">
						 <span class="serv-rating"><i class="fa-solid fa-star"></i><?php echo esc_attr(round($rating_value, 1));	?></span>
			   </div>
	   </div>
               </div>
			
								<div class="service-content">
								<h3 class="title"><a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a></h3>
								<p><i class="feather-map-pin"></i><?php the_listing_address(); ?><span class="rate"><i class="feather-phone"></i><?php echo get_the_listing_phone(); ?></span></p>
									<div class="serv-info">
										<div class="rating">
											<i class="fas fa-star filled"></i>
 											<span><?php echo esc_attr(round($rating_value, 1)); ?> (<?php echo get_comments_number($idd); ?> <?php echo esc_html_e('Reviews','truelysell_elementor');?> )</span>
										</div>
										<h6><?php  $currency_abbr = get_option('truelysell_currency');
                                                $currency_symbol = \Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
                                        $normal_price = $currency_symbol . (float) get_post_meta(get_the_ID(), '_normal_price', true); 
                                        echo esc_html($normal_price);
                                        ?></h6>
									</div>
								</div>
							</div>

</div>
 
                    <?php endwhile; // end of the loop. 
            } ?>

				 
</div>
 		 
<?php }  else if($settings['style']=='style2') { ?>
        <!-- Carousel / Start -->
       <div class="row">
         <?php 
            if ( $wp_query->have_posts() ) {
               
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					  $idd = $wp_query->post->ID;
					  $is_featured = truelysell_core_is_featured($idd); 
					  ?>
<div class="col-lg-4 col-md-6 col-sm-6 col-12">
<div class="service-widget alternate service-two aos" data-aos="fade-up">
								 

				 <div class="service-img">
                <?php $template_loader->get_template_part('content-listing-image-small');  
                $rating_value = get_post_meta($idd, 'truelysell-avg-rating', true);
                ?>
  		<div class="fav-item">
 
										<?php 
                            $terms = get_the_terms($idd, 'listing_category' ); 
                            if ( $terms && ! is_wp_error( $terms ) ) :  
                                $main_term = array_pop($terms); ?>
                                <span class="item-cat"><?php echo $main_term->name; ?></span>
                            <?php endif; ?>

										<?php
								if (truelysell_core_check_if_bookmarked($idd)) {
									$nonce = wp_create_nonce("listee_core_bookmark_this_nonce"); ?>
									<span class="serv-rating like-icon fav-icon listee_core-unbookmark-it liked" data-post_id="<?php echo esc_attr($idd); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="feather-heart"></i></span>
									<?php } else {
									if (is_user_logged_in()) {
										$nonce = wp_create_nonce("listee_core_remove_fav_nonce"); ?>
										<span class="serv-rating save fav-icon listee_core-bookmark-it like-icon" data-post_id="<?php echo esc_attr($idd); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="feather-heart"></i></span>
									<?php } else { ?>
										<span class=" serv-ratingsave fav-icon like-icon tooltip left" title="<?php esc_html_e('Login To Bookmark Items', 'listee_core'); ?>"><i class="feather-heart"></i></span>
									<?php } ?>
								<?php } ?>	
			 </div>
                     
 						<div class="item-info">
								<span class="item-img"> 
								<?php $author_id=get_the_author_meta('ID');  ?>
                                <?php echo get_avatar($author_id, 56);  ?> <?php echo get_author_name(); ?></span> 
						 </div>
             </div>
			
								<div class="service-content">
								<h3 class="title"><a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a></h3>
								<p><i class="feather-map-pin"></i><?php the_listing_address(); ?><span class="rate"><i class="feather-phone"></i><?php echo get_the_listing_phone(); ?></span></p>
									<div class="serv-info">
										<div class="rating">
											<i class="fas fa-star filled"></i>
 											<span><?php echo esc_attr(round($rating_value, 1)); ?> (<?php echo get_comments_number($idd); ?> <?php echo esc_html_e('Reviews','truelysell_elementor');?> )</span>
										</div>
										<h6><?php  $currency_abbr = get_option('truelysell_currency');
                                                $currency_symbol = \Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
                                        $normal_price = $currency_symbol . (float) get_post_meta(get_the_ID(), '_normal_price', true); 
                                        echo esc_html($normal_price);
                                        ?></h6>
									</div>
								</div>
							</div>

</div>
 
                    <?php endwhile; // end of the loop. 
            } ?>

				 
</div>

<?php }  else if($settings['style']=='style3') { ?>
        <!-- Carousel / Start -->
       <div class="row">
         <?php 
            if ( $wp_query->have_posts() ) {
               
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					  $idd = $wp_query->post->ID;
					  $is_featured = truelysell_core_is_featured($idd); 
					  ?>
<div class="col-lg-4 col-md-6 col-sm-6 col-12">
<div class="service-slider aos aos-init aos-animate" data-aos="fade-right">
							<div class="service-widget">
								<div class="service-img service-show-img">
								<a href="<?php the_permalink();?>"> <?php $template_loader->get_template_part('content-listing-image-small');   ?> </a>
									 <?php $rating_value = get_post_meta($idd, 'truelysell-avg-rating', true); ?>
									<div class="item-info item-infos">
										<div class="rating">
											<?php echo truelysell_display_course_rating($rating_value);  ?>
 										</div>
									</div>
								</div>
								<div class="service-content service-content-three">
									<h3 class="title">
										<a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a>
									</h3>
									<ul>
									<?php 
                            $terms = get_the_terms($idd, 'listing_category' ); 
                            if ( $terms && ! is_wp_error( $terms ) ) :  
                                $main_term = array_pop($terms); ?>
                                <li class="item-cat"><?php echo $main_term->name; ?></li>
                            <?php endif; ?>
							</ul>
									<div class="main-saloons-profile">
										<div class="saloon-profile-left">
										<?php $author_id=get_the_author_meta('ID');  ?>
											<div class="saloon-img">
											<?php echo get_avatar($author_id, 56);  ?> 
											</div>
											<div class="saloon-content">
												<div class="saloon-content-top">
													<i class="feather-user"></i>
													<span>  <?php echo get_author_name(); ?> </span>
												</div>
												<div class="saloon-content-btn">
													<span><i class="feather-map-pin"></i></span>
													<span><?php the_listing_address(); ?></span>
												</div>											
											</div>
										</div>
										<div class="saloon-right">
											<span><?php  $currency_abbr = get_option('truelysell_currency');
                                                $currency_symbol = \Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
                                        $normal_price = $currency_symbol . (float) get_post_meta(get_the_ID(), '_normal_price', true); 
                                        echo esc_html($normal_price);
                                        ?></span>
										</div>
									</div>
									<div class="saloon-bottom">
									<a href="<?php the_permalink();?>"><i class="feather-calendar me-2"></i><?php echo esc_html_e('MAKE AN APPOINTMENT','truelysell_elementor'); ?></a>
									</div>
								</div>
							</div>				
						</div>
</div>
 <?php endwhile; // end of the loop.  ?>
<?php } ?>				 
</div>


<?php }  else if($settings['style']=='style4') { ?>
        <!-- Carousel / Start -->
       <div class="row">
         <?php 
            if ( $wp_query->have_posts() ) {
               
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					  $idd = $wp_query->post->ID;
					  $is_featured = truelysell_core_is_featured($idd); 
					  ?>

 <div class="col-lg-4 col-md-6 col-12">
 <div class="service-widget features-service-five-main aos" data-aos="fade-up">
							<div class="service-img">
							<a href="<?php the_permalink();?>"> <?php $template_loader->get_template_part('content-listing-image-small');   ?> </a>
									 <?php $rating_value = get_post_meta($idd, 'truelysell-avg-rating', true); ?>
								<div class="fav-item ">
									<div class="features-service-five">
										<div class="features-service-rating">
 											<i class="fas fa-star filled"></i>
 											<span><?php echo esc_attr(round($rating_value, 1)); ?></span>

										</div>
										
										<?php if($is_featured ) { ?> 
											<h6><?php echo esc_html_e('Featured','truelysell_elementor'); ?></h6> 
											<?php  } ?>
									</div>
 
									<?php
								if (truelysell_core_check_if_bookmarked($idd)) {
									$nonce = wp_create_nonce("listee_core_bookmark_this_nonce"); ?>
									<span class="fav-icon-five like-icon fav-icon listee_core-unbookmark-it liked" data-post_id="<?php echo esc_attr($idd); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="feather-heart"></i></span>
									<?php } else {
									if (is_user_logged_in()) {
										$nonce = wp_create_nonce("listee_core_remove_fav_nonce"); ?>
										<span class="fav-icon-five save fav-icon listee_core-bookmark-it like-icon" data-post_id="<?php echo esc_attr($idd); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="feather-heart"></i></span>
									<?php } else { ?>
										<span class="fav-icon-five save fav-icon like-icon tooltip left" title="<?php esc_html_e('Login To Bookmark Items', 'listee_core'); ?>"><i class="feather-heart"></i></span>
									<?php } ?>
								<?php } ?>	

								</div>
							</div>
							<div class="service-content service-feature-five">
								<h3 class="title">
								<a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a>
								</h3>
								<p><i class="feather-map-pin"></i><?php the_listing_address(); ?></p>
								<div class="feature-services-five">
									<h6><?php  $currency_abbr = get_option('truelysell_currency');
                                                $currency_symbol = \Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
                                        $normal_price = $currency_symbol . (float) get_post_meta(get_the_ID(), '_normal_price', true); 
                                        echo esc_html($normal_price);
                                        ?></h6>
  								</div>
								<div class="feature-service-botton">
									<div class="feature-service-btn">
										<a href="<?php the_permalink();?>"><?php echo esc_html_e('Book Service','truelysell_elementor'); ?></a>
									</div>
									<?php $author_id=get_the_author_meta('ID');  ?>
									<?php echo get_avatar($author_id, 56);  ?> 
								</div>
							</div>
						</div>
 </div>
 <?php endwhile; // end of the loop.  ?>
 <?php if($settings['link']!='') { ?>
 <div class="btn-sec btn-sec-five aos" data-aos="fade-up">
					<a href="<?php echo $settings['link']; ?>" class="btn btn-primary btn-view"><?php echo $settings['label']; ?></a>
 </div>
 <?php } ?>				
<?php } ?>				 
</div>

<?php }  else if($settings['style']=='style5') { ?>
        <!-- Carousel / Start -->
       <div class="row">
         <?php 
            if ( $wp_query->have_posts() ) {
               
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					  $idd = $wp_query->post->ID;
					  $is_featured = truelysell_core_is_featured($idd); 
					  ?>

<div class="col-lg-3 col-md-6 col-sm-6 col-12">
						<div class="service-widget service-widget-eight aos" data-aos="fade-up">
							<div class="service-img">
							<a href="<?php the_permalink();?>"> <?php $template_loader->get_template_part('content-listing-image-small');   ?> </a>
									 <?php $rating_value = get_post_meta($idd, 'truelysell-avg-rating', true); ?>
								<div class="fav-item ">
									<div class="features-service-five features-service-eight">
 										<?php 
                            $terms = get_the_terms($idd, 'listing_category' ); 
                            if ( $terms && ! is_wp_error( $terms ) ) :  
                                $main_term = array_pop($terms); ?>
                                <h6><?php echo $main_term->name; ?></h6>
                            <?php endif; ?>

									</div>
									<?php
								if (truelysell_core_check_if_bookmarked($idd)) {
									$nonce = wp_create_nonce("listee_core_bookmark_this_nonce"); ?>
									<span class="fav-icon-five like-icon fav-icon listee_core-unbookmark-it liked" data-post_id="<?php echo esc_attr($idd); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="feather-heart"></i></span>
									<?php } else {
									if (is_user_logged_in()) {
										$nonce = wp_create_nonce("listee_core_remove_fav_nonce"); ?>
										<span class="fav-icon-five save fav-icon listee_core-bookmark-it like-icon" data-post_id="<?php echo esc_attr($idd); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="feather-heart"></i></span>
									<?php } else { ?>
										<span class="fav-icon-five save fav-icon like-icon tooltip left" title="<?php esc_html_e('Login To Bookmark Items', 'listee_core'); ?>"><i class="feather-heart"></i></span>
									<?php } ?>
								<?php } ?>	
								</div>
							</div>
							<div class="service-content service-feature-eight">
								<div class="shop-content-logo">
								<?php $author_id=get_the_author_meta('ID');  ?>
									<?php echo get_avatar($author_id, 56);  ?> 
								</div>
								<h3 class="title">
							      	<a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a>
								</h3>
								<p><i class="feather-map-pin"></i><?php the_listing_address(); ?></p>
								<div class="feature-services-eight">
									<div class="popular-service-eight">
										<h6><?php  $currency_abbr = get_option('truelysell_currency');
                                                $currency_symbol = \Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
                                        $normal_price = $currency_symbol . (float) get_post_meta(get_the_ID(), '_normal_price', true); 
                                        echo esc_html($normal_price);
                                        ?></h6>
 									</div>
									<div class="rate">
										<div class="rate-icon">
											<i class="fas fa-star filled"></i><span><?php echo esc_attr(round($rating_value, 1)); ?></span>
										</div>
										<h6>(<?php echo get_comments_number($idd); ?>)</h6>
									</div>
								</div>
							</div>
						</div>
					</div>

  
 <?php endwhile; // end of the loop.  ?>
 <?php if($settings['link']!='') { ?>
 <div class="btn-sec btn-saloons btn-pets aos aos-init aos-animate" data-aos="fade-up">
					<a href="<?php echo $settings['link']; ?>" class="btn btn-primary btn-view"><?php echo $settings['label']; ?></a>
 </div>
 <?php } ?>				
<?php } ?>				 
</div>
<?php }  else if($settings['style']=='style6') { ?>
        <!-- Carousel / Start -->
       <div class="row">
         <?php 
            if ( $wp_query->have_posts() ) {
               
                    while ( $wp_query->have_posts() ) : $wp_query->the_post(); 
					  $idd = $wp_query->post->ID;
					  $is_featured = truelysell_core_is_featured($idd); 
					  ?>

<div class="col-lg-4 col-md-6 col-sm-6 col-12">
						<div class="service-widget service-widget-nine">
							<div class="service-img">
							<a href="<?php the_permalink();?>"> <?php $template_loader->get_template_part('content-listing-image-small');   ?> </a>
									 <?php $rating_value = get_post_meta($idd, 'truelysell-avg-rating', true); ?>
								<div class="item-info items-nine">
								<?php $author_id=get_the_author_meta('ID');  ?>
									<?php echo get_avatar($author_id, 56);  ?> 
									<div class="rating">
									<?php echo truelysell_display_course_rating($rating_value);  ?>
										<span>(<?php echo get_comments_number($idd); ?>)</span>
									</div>
								</div>
							</div>
							<div class="service-content service-content-nine">
 								<?php 
                            $terms = get_the_terms($idd, 'listing_category' ); 
                            if ( $terms && ! is_wp_error( $terms ) ) :  
                                $main_term = array_pop($terms); ?>
                                <span class="sub-title"><?php echo $main_term->name; ?></span>
                            <?php endif; ?>

								<h3 class="title">
									<a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a>
								</h3>
								<div class="main-saloons-profile">
									<div class="saloon-profile-left">
										<div class="saloon-content">
											<div class="saloon-content-top">
												<i class="feather-phone"></i>
												<span><?php echo get_the_listing_phone(); ?></span>
											</div>
											<div class="saloon-content-btn">
												<span><i class="feather-map-pin"></i></span>
												<span><?php the_listing_address(); ?></span>
											</div>
										</div>
									</div>
									<div class="saloon-right">
										<span><?php  $currency_abbr = get_option('truelysell_currency');
                                                $currency_symbol = \Truelysell_Core_Listing::get_currency_symbol($currency_abbr);
                                        $normal_price = $currency_symbol . (float) get_post_meta(get_the_ID(), '_normal_price', true); 
                                        echo esc_html($normal_price);
                                        ?></span>
									</div>
								</div>
								<div class="service-bottom-nine">
									<a href="<?php the_permalink();?>" class="btn btn-primary"><?php echo esc_html_e('Make An Appointment','truelysell_elementor');?></a>
								</div>
							</div>
						</div>
					</div>
  
 <?php endwhile; // end of the loop.  ?>
 <?php if($settings['link']!='') { ?>
 <div class="btn-sec btn-service-nine aos aos-init aos-animate" data-aos="fade-up">
					<a href="<?php echo $settings['link']; ?>" class="btn btn-primary btn-view"><?php echo $settings['label']; ?></a>
 </div>
 <?php } ?>				
<?php } ?>				 
</div>
  <?php } ?>
         <?php wp_reset_postdata();
        wp_reset_query();
        echo ob_get_clean();
	}
		protected function get_terms($taxonomy) {
			$taxonomies = get_terms( array( 'taxonomy' =>$taxonomy,'hide_empty' => false) );

			$options = [ '' => '' ];
			
			if ( !empty($taxonomies) ) :
				foreach ( $taxonomies as $taxonomy ) {
					if($taxonomy){
					//$options[ $taxonomy->slug ] = $taxonomy->name;
								}
				}
			endif;

			return $options;
		}

		protected function get_posts() {
			$posts = get_posts( 
				array( 
					'numberposts' => -1, 
					'post_type' => 'listing', 
					'suppress_filters' =>true
				) );

			$options = [ '' => '' ];
			
			if ( !empty($posts) ) :
				foreach ( $posts as $post ) {
					$options[ $post->ID ] = get_the_title($post->ID);
				}
			endif;

			return $options;
		}
	
}