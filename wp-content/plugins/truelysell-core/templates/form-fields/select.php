<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field = $data->field;

$key = $data->key;

$multi = false;
$css_class = 'select2-single form-control';
if(isset($field['multi']) && $field['multi']) {
	$multi = true;
	$css_class = 'select2-multiple';
}

if(isset( $field['options_cb'] ) && !empty($field['options_cb'])){
	switch ($field['options_cb']) {
		case 'truelysell_core_get_offer_types_flat':
			$field['options'] = truelysell_core_get_offer_types_flat(false);
			break;

		case 'truelysell_core_get_listing_types':
			$field['options'] = truelysell_core_get_listing_types();
			break;

		case 'truelysell_core_get_rental_period':
			$field['options'] = truelysell_core_get_rental_period();
			break;

		// case 'timezone':
		// 	$default = CMB2_Utils::timezone_string();
		// 	$field['options'] = wp_timezone_choice($default);
		// 	break;
		
		default:
			# code...
			break;
	}	
}



?>

<select <?php if($multi) echo "multiple"; ?> class="<?php if($multi) { echo "select2-multiple"; } else { echo "select2-single"; }  ?> <?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : $key ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key );  if($multi) echo "[]"; ?>" id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>>
	
	<?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
		<option value=""><?php echo esc_attr($field['placeholder']);?></option>
	<?php endif ?>

	<?php foreach ( $field['options'] as $key => $value ) : ?>

		<option value="<?php echo esc_attr( $key ); ?>" <?php

			if( isset( $field['value']) && is_array( $field['value'] ) ) {
				
				if(isset($field['value'][0]) && !empty($field['value'][0])){
				
						if(in_array($key, $field['value'])){
						echo 'selected="selected"';
				
					}	
				}

			} else {

				if ( isset( $field['value'] ) || isset( $field['default'] ) ) selected( isset( $field['value'] ) ? 
					$field['value'] : $field['default'], $key ); 
			}
			
			?>><?php echo esc_html( $value ); ?></option>

	<?php endforeach; ?>

</select>