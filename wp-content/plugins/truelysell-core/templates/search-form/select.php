<?php
if(isset($data->options_cb) && !empty($data->options_cb)){

	switch ($data->options_cb) {
		case 'truelysell_core_get_offer_types':
			$data->options = truelysell_core_get_offer_types_flat(false);
			break;

		case 'truelysell_get_listing_types':
			$data->options = truelysell_core_get_listing_types();
			break;

		case 'truelysell_core_get_rental_period':
			$data->options = truelysell_core_get_rental_period();
			break;
		
		default:
			# code...
			break;
	}	
}

if(isset($_GET[$data->name])) {
	$selected = sanitize_text_field($_GET[$data->name]);
} else {
	$selected = '';
	if(isset($data->default) && !empty($data->default)){
		$selected = $data->default;
	} else {
		$selected = '';	
	}
} 
?>
 
<div class="filter-content">
<h2><?php echo esc_attr($data->labeltext);?></h2>
<div class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
		<select <?php if( isset($data->multi) && $data->multi == '1') { echo 'multiple class="select2-multiple"'; } else { echo 'class="select2-single"'; } ?> name="<?php echo esc_attr($data->name);?>" id="<?php echo esc_attr($data->name);?>"  data-placeholder="<?php echo esc_attr($data->placeholder);?>"  >
			<option value="-1"><?php echo esc_attr($data->placeholder);?></option> 
			<?php 
			if( is_array( $data->options ) ) :
				foreach ($data->options as $key => $value) { ?>
					<option <?php selected($selected, $key) ?> value="<?php echo esc_html($key);?>"><?php echo esc_html($value);?></option>
				<?php }
			endif;
			?>
		</select>
</div>
</div>