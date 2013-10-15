<?php
/*
Plugin Name: Google Maps field for Types
Plugin URI: http://khromov.wordpress.com
Description: Provides a Google Maps field type for Toolset Types
Version: 1.0
Author: khromov
Author URI: http://khromov.wordpress.com
License: GPL2
*/

/*
  This is example how to add simple Google Maps field type.
  Most of functions are called automatically from Types plugin.
  Functions naming conventions are:

  - For basic type data (required) callback
  wpcf_fields_$myfieldname()

  Optional

  - Group form data callback
  wpcf_fields_$myfieldname_insert_form()

  - Post edit page form callback
  wpcf_fields_$myfieldname_meta_box_form()

  - Editor popup callback
  wpcf_fields_$myfieldname_editor_callback()

  - View function callback
  wpcf_fields_$myfieldname_view()

 */

/** Load dependencies **/
if ( ! class_exists( 'MicroTemplate_v3' ) && ! class_exists( 'MT_v3' ) )
	include( 'lib/microtemplate.class.php' );

//Add registration hook
add_filter( 'types_register_fields', 'types_simple_google_map' );

/**
 * Register custom post type on 'types_register_fields' hook.
 *
 * @param array $fields
 * @return type
 */
function types_simple_google_map( $fields ) {
	$fields['simple_google_map'] = __FILE__;
	return $fields;
}

/**
 * Define field.
 *
 * @return type
 */
function wpcf_fields_simple_google_map() {
	return array(
		'path' => __FILE__, // This is required
		'id' => 'simple_google_map',
		'title' => __( 'Simple Google Map'),
		'description' => __( 'This is additional field'),
		/*
		 * Validation
		 */
		'validate' => array('required'),

		/*
		// Additional JS on post edit page
		'meta_box_js' => array(// Add JS when field is active on post edit page
			'wpcf-jquery-fields-my-field' => array(
				'inline' => 'wpcf_fields_google_map_meta_box_js_inline', // This calls function that renders JS
				'deps' => array('jquery'), // (optional) Same as WP's enqueue_script() param
				'in_footer' => true, // (optional) Same as WP's enqueue_script() param
			),
			'wpcf-jquery-fields-my-field' => array(
				'src' => get_stylesheet_directory_uri() . '/js/my-field.js', // This will load JS file
			),
		),
		// Additional CSS on post edit page
		'meta_box_css' => array(
			'wpcf-jquery-fields-my-field' => array(
				'src' => get_stylesheet_directory_uri() . '/css/my-field.css', // or inline function 'inline' => $funcname
				'deps' => array('somecss'), // (optional) Same as WP's enqueue_style() param
			),
		),
		// Additional JS on group edit page
		'group_form_js' => array(// Add JS when field is active on post edit page
			'wpcf-jquery-fields-my-field' => array(
				'inline' => 'wpcf_fields_google_map_group_form_js_inline', // This calls function that renders JS
				'deps' => array('jquery'), // (optional) Same as WP's enqueue_script() param
				'in_footer' => true, // (optional) Same as WP's enqueue_script() param
			),
			'wpcf-jquery-fields-my-field' => array(
				'src' => get_stylesheet_directory_uri() . '/js/my-field.js', // This will load JS file
			),
		),
		// Additional CSS on post edit page
		'group_form_css' => array(
			'wpcf-jquery-fields-my-field' => array(
				'src' => get_stylesheet_directory_uri() . '/css/my-field.css', // or inline function 'inline' => $funcname
				'deps' => array('somecss'), // (optional) Same as WP's enqueue_style() param
			),
		),
		// override editor popup link (you must then load JS function that will process it)
		//'editor_callback' => 'wpcfFieldsMyFieldEditorCallback(\'%s\')', // %s will inject field ID
		*/

		// meta key type
		'meta_key_type' => 'INT',
		// Required WP version check
		'wp_version' => '3.3',
	);
}

/**
 * Types Group edit screen form.
 *
 * Here you can specify all additional group form data if nedded,
 * it will be auto saved to field 'data' property.
 *
 * @return string
 */
function wpcf_fields_simple_google_map_insert_form() {

	$form['additional'] = array(
		'#type' => 'textfield',
		'#description' => 'Add some comment',
		'#name' => 'comment',
	);
	return $form;

}

/**
 * Overrides form output in meta box on post edit screen.
 */
function wpcf_fields_simple_google_map_meta_box_form( $data ) {
	$form['name'] = array(
		'#name' => 'wpcf[' . $data['slug'] . ']', // Set this to override default output
		'#type' => 'textfield',
		'#title' => __( 'Enter address or coordinates'),
		'#description' => __('gmaps description + logo') //FIXME
	);
	return $form;
}

/**
 * Adds editor popup callnack.
 *
 * This form will be showed in editor popup
 */
function wpcf_fields_simple_google_map_editor_callback( $field, $settings )
{
	ob_start();	?>

	<!-- CSS -->
	<style type="text/css">
		#types-google-maps-container
		{
			margin: 0;
			padding: 0;
		}
		#types-google-maps-container input[type='text']
		{
			width: 45px;
		}
		#types-google-maps-container span.info
		{
			display: block;
			margin-top: 5px;
			color: gray;
			font-style: italic;
		}
		#types-google-maps-container hr
		{
			display: block;
			height: 1px;
			border: 0;
			border-top: 1px solid #ccc;
			margin: 1em 0;
		}
		#types-google-maps-container ul.form-list > li > input
		{
			float: left;
			margin-right: 10px;
		}
		#types-google-maps-container ul.form-list
		{
			overflow: hidden;
		}
	</style>

	<!-- JavaScript -->
	<script type="text/javascript">
		jQuery( document ).ready(function()
		{
			jQuery("input[name=maptypeselectgroup]").click(function()
			{
				alert("selected");
			});
		});
		function toggle_boxes_info()
		{
			
		}
	</script>

	<div id="types-google-maps-container">
		<h3>
			<?php _e('Google Maps Integration'); ?>
		</h3>
		<ul class="form-multichoice-list form-list">
			<li>
				<input type="radio" name="maptypeselectgroup" value="iframe" id="apiselect-iframe" checked>
				<label for="apiselect-iframe">
					<?php _e('iFrame API'); ?>
					<a href="http://dn.se" target="_blank">
						<?php _e('?'); ?>
					</a>
				</label>
			</li>
			<li>

				<input type="radio" name="maptypeselectgroup" value="image" id="apiselect-image">
				<label for="apiselect-image">
					<?php _e('Image API'); ?>
					<a href="http://dn.se" target="_blank">
						<?php _e('?'); ?>
					</a>
				</label>


			</li>
		</ul>

		<hr/>

		<h3>
			Map display settings
		</h3>

		<input type="radio" name="fluid" value="1"> <?php _e('Fluid (100% of container)'); ?> <br>
		<input type="radio" name="fluid" value="0" checked> <?php _e('Fixed width'); ?> <br>

		<hr/>

		<h3>
			Fixed width settings
		</h3>

		<span class="info" id="info-api-image">
			<?php _e('When using the Image API, enter the width and height as numbers only (no percent allowed). You can style the image output via CSS.'); ?>
		</span>

		<span style="display: block; margin-top: 5px; color: gray; font-style: italic; ">
			<?php _e('Enter the width and height as numbers only. Add the % sign to use a fluid layout. Example: 456, 25%'); ?>
		</span>

		<label>
			<input type="text" name="width" value="<?php echo isset( $settings['width'] ) ? $settings['width'] : '425'; ?>" />&nbsp;
			<?php _e('Width'); ?>
		</label>


		<label>
			<input type="text" name="height" value="<?php echo isset( $settings['height'] ) ? $settings['height'] : '350'; ?>" />&nbsp;<?php _e('Height', 'wpcf'); ?>
		</label>

		<!--
		<label>
			<input type="checkbox" name="image" value="1">&nbsp;<?php _e('Use Google Maps Image API instead of iframe', 'wpcf'); ?>
		</label>
		-->

		<h3>
			Zoom level
		</h3>
		<input id="defaultSlider" type="range" min="1" max="32" value="13" />

		<!-- FIXME: make it purdy<br/> -->
		<output onforminput="value=range.value">
			0
		</output>

		<label>
			<input type="text" name="image_zoomlevel" value="<?php echo isset( $settings['image_zoomlevel'] ) ? $settings['image_zoomlevel'] : '13'; ?>" />
			<br/>
			<?php _e('Zoom level (For Image API only)', 'wpcf'); ?>
		</label>
	</div>

	<?php
	$form = ob_get_contents();
	ob_get_clean();

	return array(
		'tabs' => array(
			'display' => array(
				'menu_title' => __( 'Display', 'wpcf' ),
				'title' => __( 'Google Maps Settings', 'wpcf' ),
				'content' => $form,
			)
		)
	);
}

/**
 * Processes editor popup submit
 */
function wpcf_fields_simple_google_map_editor_submit( $data, $field ) {
	$add = '';

	// Add parameters
	if ( !empty( $data['width'] ) ) {
		$add .= ' width="' . strval( $data['width'] ) . '"';
	}
	if ( !empty( $data['height'] ) ) {
		$add .= ' height="' . strval( $data['height'] ) . '"';
	}
	if ( !empty( $data['image'] ) ) {
		$add .= ' image="' . intval($data['image']) . '"';
	}
	if ( !empty( $data['image'] ) ) {
		$add .= ' image_zoomlevel="' . intval($data['image_zoomlevel']) . '"';
	}

	if(!empty($data['maptypeselectgroup']))
	{
		$add .= " maptypeselectgroup=\"{$data['maptypeselectgroup']}\"";
	}

	// Generate and return shortcode
	return wpcf_fields_get_shortcode( $field, $add );
}

/**
 * Renders view
 *
 * Useful $data:
 * $data['field_value'] - Value of custom field
 *
 * @param array $data
 */
function wpcf_fields_simple_google_map_view( $data ) {
	$data['image'] = !empty( $data['image'] ) ? intval($data['image']) : 0;
	$data['width'] = !empty( $data['width'] ) ? $data['width'] : 425;
	$data['height'] = !empty( $data['height'] ) ? $data['height'] : 350;
	$data['image_zoomlevel'] = !empty( $data['image_zoomlevel'] ) ? $data['image_zoomlevel'] : 13;

	if($data['image'] === 1)
	{
		ob_start(); ?>
		<a href="https://maps.google.com/maps?q=<?php echo $data['field_value']; ?>" target="_blank" class="types-google-map-url">
			<img src="https://maps.google.com/maps/api/staticmap?zoom=<?php echo $data['image_zoomlevel']; ?>&markers=size:small|color:blue|<?php echo $data['field_value']; ?>&size=<?php echo $data['width']; ?>x<?php echo $data['height']; ?>&sensor=false" class="types-google-map-image"/>
		</a>
		<?php return ob_get_clean();
	}
	else
	{
		ob_start(); ?>
		<iframe
			width="<?php echo $data['width']; ?>"
			height="<?php echo $data['height']; ?>"
			frameborder="0"
			scrolling="no"
			marginheight="0"
			marginwidth="0" src="https://maps.google.com/maps?q=<?php echo $data['field_value']; ?>&output=embed"
			class="types-google-map-iframe">
		</iframe>
		<?php return ob_get_clean();
	}
}