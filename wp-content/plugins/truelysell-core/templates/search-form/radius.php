<?php 
$flag_enabled = false;
if(isset($_GET[$data->name.'_min']) && !empty($_GET[$data->name.'_min']) && $_GET[$data->name.'_min'] != 'NaN') {
	$min = sanitize_text_field($_GET[''.$data->name.'_min']);
	$min = (int)preg_replace('/[^0-9]/', '', $min);
	if($data->name == '_price'){
		$data->min = Truelysell_Core_Search::get_min_meta_value($data->name);
	} else {
		$data->min = Truelysell_Core_Search::get_min_meta_value($data->name);
	}
} else {
	if($data->min == 'auto') {
		if($data->name == '_price'){
			$min = Truelysell_Core_Search::get_min_meta_value($data->name);
		} else {
			$min = Truelysell_Core_Search::get_min_meta_value($data->name);
			$data->min = Truelysell_Core_Search::get_min_meta_value($data->name);
		}
		
	} else {
		$min = $data->min;	
	}
} 

if(isset($_GET[$data->name.'_max']) && !empty($_GET[$data->name.'_max']) && $_GET[$data->name.'_max'] != 'NaN') {
	$max = sanitize_text_field($_GET[$data->name.'_max']);
	$max = (int)preg_replace('/[^0-9]/', '', $max);
	if($data->name == '_price'){
		$data->max = Truelysell_Core_Search::get_max_meta_value($data->name,'sale');
	} else {
		$data->max = Truelysell_Core_Search::get_max_meta_value($data->name);
	}
} else {
	if($data->max == 'auto') {
		if($data->name == '_price'){
			$max = Truelysell_Core_Search::get_max_meta_value($data->name,'sale');
		} else {
			$max = Truelysell_Core_Search::get_max_meta_value($data->name);
			$data->max = Truelysell_Core_Search::get_max_meta_value($data->name);
		}
	} else {
		$max = $data->max;	
	}
	
} 

?>
<!-- Area Range -->
<div class="<?php if(isset($data->class)) { echo esc_attr($data->class); } ?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>" >
	<div class="range-slider">
		<input name="<?php echo esc_attr($data->name); ?>" class="distance-radius" type="range" min="<?php echo esc_attr($data->min); ?>" max="<?php echo esc_attr($data->max); ?>" step="1" value="<?php echo get_option('truelysell_maps_default_radius'); ?>" data-title="<?php echo esc_html($data->placeholder) ?>">
	</div>

</div>