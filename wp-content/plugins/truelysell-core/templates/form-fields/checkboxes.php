<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;
$value = isset( $field['value'] ) ? $field['value'] : array();
if($value==''){
	$value = array();
}

?>

<?php if(isset($field['form_type']) && $field['form_type'] == 'registration') { ?>
	<label class="truelysell_core-checkboxes-label"><?php echo $field['placeholder']; ?></label>	
<?php } ?>

<div class="checkboxes in-row margin-bottom-20">

	<?php foreach ( $field['options'] as $slug => $name ) : ?>

		<input id="<?php echo esc_html($slug) ?>" type="checkbox" name="<?php echo $key.'[]'; ?>"
		<?php  if(is_array($value) && in_array($slug,$value))  : ?> checked="checked" <?php endif; ?> value="<?php echo esc_html($slug); ?>"
		>
		<label for="<?php echo esc_html($slug) ?>"><?php echo esc_html($name) ?></label>
	<?php endforeach; ?>

</div>