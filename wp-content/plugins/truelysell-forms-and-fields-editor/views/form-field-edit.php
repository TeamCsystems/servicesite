<div class="edit-form-field" style="display: none;">

    <div id="truelysell-field-<?php echo $field_key; ?>">

    	 <p class="name-container">
            <label for="label">Name</label>
            <input type="text" class="input-text" name="name[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( $field['name'] ); ?>" />
        </p>  
        <?php 
        $blocked_fileds = array('_price','_price_per','_offer_type','_property_type','_rental_period','_area','_friendly_address','_address','_geolocation_lat','_geolocation_long'); 

        ?>
		
		<p class="field-id" 
		<?php if( isset($field['id']) && in_array($field['id'],$blocked_fileds)) { echo 'style="display:none"'; } ?>>
			<label for="label">ID <span class="dashicons dashicons-editor-help" title="Do not edit if you don't know what you are doing :)"></span></label>
			<input type="text" class="input-text" name="id[<?php echo esc_attr( $index ); ?>]" value="<?php echo esc_attr( isset( $field['id'] ) ? $field['id'] : '' ); ?>"   />
		</p>
		<p class="field-type">
			<label for="type">Type</label>
			<select name="type[<?php echo esc_attr( $index ); ?>]">
				<?php
				foreach ( $field_types as $key => $type ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $field['type'], $key, false ) . '>' . esc_html( $type ) . '</option>';
				}
				?>
			</select>
		</p>
		 
		<?php if( in_array($tab,array('events_tab','service_tab','rental_tab','classifieds_tab')) ) : ?>
		<p class="invert-container">
            <label for="invert">Show value below label</label>
            <input name="invert[<?php echo esc_attr( $index ); ?>]" type="checkbox" <?php if(isset($field['invert'])) checked(  $field['invert'], 1, true ); ?> value="1">
        </p>
    	<?php endif; ?>
		<p>
			<label for="desc">Decription <span class="dashicons dashicons-editor-help" title="Description for the field, displayed in back-end"></span></label>
			<textarea  rows="4" cols="10" class="input-text" name="desc[<?php echo esc_attr( $index ); ?>]"><?php if(isset( $field['desc'] )) { echo esc_attr( $field['desc'] ); } ?></textarea>
		</p>
		<div class="field-options">
			<label for="options">Options</label>
			<?php 
			$source = '';
			if(!isset($field['options_source'])) {
				if( isset($field['options_cb']) && !empty($field['options_cb']) ) {
				 	$source = 'predefined';
				}; 
			} else {
				$source = '';
			};

			if(isset($field['options_source']) && empty($field['options_source'])) {
				if( isset($field['options_cb']) && !empty($field['options_cb'])) {
				 	$source = 'predefined';
				}; 
			} 
			if(isset($field['options_source']) && !empty($field['options_source'])) {
				$source = $field['options_source'];
			} ?>
			<div class="options" >
				
				<table  class="field-options-custom">
					<thead>
						<tr>
							<td>Value</td>
							<td>Name</td>
							<td></td>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="3">
								<a class="add-new-option-table" href="#">Add</a>
							</td>
						</tr>
					</tfoot>
					<tbody data-field="<?php echo esc_attr("
					<tr>
						<td>
							<input type='text' class='input-text options' name='options[{$index}][-1][name]' />
						</td>
						<td>
							<input type='text' class='input-text options' name='options[{$index}][-1][value]' />
						</td>
						<td class='remove_row'>x</td>
					</tr>"); ?>">
						<?php if(isset($field['options']) && is_array($field['options'])) { 
							 $i = 0;
							foreach ($field['options'] as $key => $value) {
							?>
						<tr>
							<td>
	<input type="text" value="<?php echo esc_attr($key);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr($i); ?>][name]" />
							</td>
							<td>
	<input type="text" value="<?php echo esc_attr($value);?>" class="input-text options" name="options[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr($i); ?>][value]" />
							</td>
							<td class="remove_row">x</td>
						</tr>
							<?php 
							$i++;
							}
						}; ?>
					</tbody>
				</table>
			</div>
		</div>
		<p>
			<label for="">Default value</label>
			<input type="text" class="input-text" name="default[<?php echo esc_attr( $index ); ?>]" value="<?php if(isset( $field['default'] )) { echo esc_attr( $field['default'] ); } ?>" />
		</p>

    </div>
</div>
