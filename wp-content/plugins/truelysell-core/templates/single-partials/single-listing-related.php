<?php
//Get array of terms
$template_loader = new Truelysell_Core_Template_Loader;
$terms = get_the_terms( $post->ID , 'region', 'string');
if($terms):
//Pluck out the IDs to get an array of IDS
$term_ids = wp_list_pluck($terms,'term_id');

//Query posts with tax_query. Choose in 'IN' if want to query posts with any of the terms
//Chose 'AND' if you want to query for posts with all terms
  $second_query = new WP_Query( array(
      'post_type' => 'listing',
      'tax_query' => array(
                    array(
                        'taxonomy' => 'region',
                        'field' => 'id',
                        'terms' => $term_ids,
                        'operator'=> 'IN' //Or 'AND' or 'NOT IN'
                     )),
      'posts_per_page' => 3,
      'ignore_sticky_posts' => 1,
      'orderby' => 'rand',
      'post__not_in'=>array($post->ID)
   ) );

//Loop through posts and display...
    if($second_query->have_posts()) { ?>
    <h3 class="desc-headline no-border margin-bottom-35 margin-top-60 print-no"><?php esc_html_e('Similar Properties','truelysell_core'); ?></h3>
    <div class="listings-container list-layout print-no relateo-related-listings">
      <?php 
  	    while ($second_query->have_posts() ) : $second_query->the_post(); 
  	       $template_loader->get_template_part( 'content-listing' ); 
  		  endwhile; wp_reset_postdata(); wp_reset_query();
      ?>
    </div>
    <?php }
endif;
?>