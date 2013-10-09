<?php

class GFUploadRules {

  protected static $version = '1.0';

	public function localize() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'gforms_uprules' );
		load_textdomain( 'gforms_uprules', WP_LANG_DIR . "/gforms_uprules/gforms_uprules-$locale.mo" );
		load_plugin_textdomain( 'gforms_uprules', null, basename( plugin_dir_path( __FILE__ ) ) . 'lang' );
	}

	public function register_scripts() {
		wp_register_script('gform_uprules_plugin_form_editor', plugins_url('/js/form_editor.js', __FILE__), array('jquery'), self::$version, true );
	}

	public function editor_js() {

		if ( ! in_array( 'gform_uprules_plugin_form_editor', wp_print_scripts(array('gform_uprules_plugin_form_editor')) ) ) :
    ?>
    <script type="text/javascript" id="gform_uprules_plugin_form_editor">
    <?php include plugin_dir_path( __FILE__ ) . 'js/form_editor.js'; ?>
    </script>
    <?php
    endif;
	}

  public function dimension_field_label_minwidth() {

    $locale = apply_filters( 'plugin_locale', get_locale(), 'gforms_uprules' );
    $label_minwidth_by_locale = apply_filters( 'gforms_uprules_dimension_field_label_minwidth', array( 'en_US' => 50 ) );

    if ( isset( $label_minwidth_by_locale[ $locale ] ) )
      return $label_minwidth_by_locale[ $locale ];

    //in case somebody has messed things up
    return 50;
  }

	public function field_settings( $position ) {
		if ( 200 != $position )
			return;

    $label_minwidth = self::dimension_field_label_minwidth();
		?>
		<li class="uprules_filesize_setting field_setting">
			<label for="field_uprules_filesize">
				<?php _e("Filesize Limit", "gforms_uprules"); ?>
        <?php gform_tooltip("form_field_uprules_filesize"); ?>
			</label>
			<input type="text" id="field_uprules_filesize" style="text-align: right;" onkeyup="SetFieldProperty('uprules_filesize_limit', this.value);" size="10" />
			<select id="field_uprules_filesize_dim" onchange="SetFieldProperty('uprules_filesize_dim', jQuery(this).val() );"><option value="kb">KB</option><option value="mb">MB</option></select>
		</li>
    <li class="uprules_dimensions_setting field_setting">
      <label>
        <?php _e("Image dimensions", "gforms_uprules"); ?>

        <select id="field_uprules_dims_ruletype" onchange="SetFieldProperty('uprules_dims_ruletype', jQuery(this).val() );">
          <option value="" style="text-align: center"><?php _e("&ndash; Select &ndash;", "gforms_uprules"); ?></option>
          <option value="exact"><?php _e("Exact", "gforms_uprules"); ?></option>
          <option value="conditional"><?php _e("Conditional", "gforms_uprules"); ?></option>
        </select>

        <?php gform_tooltip("form_field_uprules_dimensions"); ?>

      </label>

      <div class="uprules_dims_fields_exact" style="display: none;">
        <div>
          <label for="uprules_dims_exact_width" class="inline" style="min-width: <?php echo esc_attr( $label_minwidth ); ?>px; margin-right: 10px;">
            <?php _e("Width", "gforms_uprules"); ?>
          </label>
          <input type="text" id="field_uprules_dims_exact_width" style="text-align: center;" onkeyup="SetFieldProperty('uprules_dims_exact_width', this.value);" size="6" />
        </div>
        <div>
          <label for="uprules_dims_exact_height" class="inline" style="min-width: <?php echo esc_attr( $label_minwidth ); ?>px; margin-right: 10px;">
            <?php _e("Height", "gforms_uprules"); ?>
          </label>
          <input type="text" id="field_uprules_dims_exact_height" style="text-align: center;" onkeyup="SetFieldProperty('uprules_dims_exact_height', this.value);" size="6" />
        </div>
      </div> <!--//-- .uprules_dims_fields_exact -->

      <div class="uprules_dims_fields_conditional" style="display: none;">
        <div class="uprules_dims_fields_conditional_min">
          <label for="uprules_dims_minwidth" class="inline" style="min-width: <?php echo esc_attr( $label_minwidth ); ?>px; margin-right: 10px;">
            <?php _e("Min", "gforms_uprules"); ?>
          </label>
          <input type="text" id="field_uprules_dims_minwidth" style="text-align: center;" onkeyup="SetFieldProperty('uprules_dims_minwidth', this.value);" size="6" placeholder="<?php esc_attr_e("Width", "gforms_uprules"); ?>" />
          &times;
          <input type="text" id="field_uprules_dims_minheight" style="text-align: center;" onkeyup="SetFieldProperty('uprules_dims_minheight', this.value);" size="6" placeholder="<?php esc_attr_e("Height", "gforms_uprules"); ?>" />
        </div>
        <div class="uprules_dims_fields_conditional_max">
          <label for="uprules_dims_maxwidth" class="inline" style="min-width: <?php echo esc_attr( $label_minwidth ); ?>px; margin-right: 10px;">
            <?php _e("Max", "gforms_uprules"); ?>
          </label>
          <input type="text" id="field_uprules_dims_maxwidth" style="text-align: center;" onkeyup="SetFieldProperty('uprules_dims_maxwidth', this.value);" size="6" placeholder="<?php esc_attr_e("Width", "gforms_uprules"); ?>" />
          &times;
          <input type="text" id="field_uprules_dims_maxheight" style="text-align: center;" onkeyup="SetFieldProperty('uprules_dims_maxheight', this.value);" size="6" placeholder="<?php esc_attr_e("Height", "gforms_uprules"); ?>" />
        </div>
      </div> <!--//-- .uprules_dims_fields_conditional -->

    </li>
		<?php
	}

  public function field_validation( $valid, $value, $form, $field ) {
    if ( ! empty( $_FILES ) && $valid['is_valid'] && in_array( RGFormsModel::get_input_type($field), array( 'fileupload', 'post_image' ) ) ) {

      $form_id = $form['id'];
      $input_name = 'input_' . $field['id'];
      $fileinfo = RGFormsModel::get_temp_filename( $form_id, $input_name );
      $temp_filepath = RGFormsModel::get_upload_path($form_id) . "/tmp/" . $fileinfo["temp_filename"];

      if ( isset( $_FILES[$input_name] ) && !empty( $_FILES[$input_name] ) )  {
        $bytes = $_FILES[$input_name]['size'];
        $dims = @getimagesize( $_FILES[$input_name]['tmp_name'] );
      }
      elseif ( file_exists( $temp_filepath ) ) {
        $bytes = filesize( $temp_filepath );
        $dims = @getimagesize( $temp_filepath );
      }
      else return $valid;


      //validate filesize
			if ( isset( $field['uprules_filesize_limit'] ) ) {
				$multipliers = array(
					'kb' => 1024,
					'mb' => 1024 * 1024
				);
				$max_filesize_user = intval( $field['uprules_filesize_limit'] );
				$bytes_multiplier = $multipliers[$field['uprules_filesize_dim']];
				$max_filesize_bytes = $max_filesize_user * $bytes_multiplier;
			}

      if ( isset( $bytes ) && $max_filesize_user > 0 && $max_filesize_bytes < $bytes ) {
        $valid['is_valid'] = false;
        $valid['message'] = sprintf( __( 'Max file upload size (%s) exceeded.', 'gravityforms' ), size_format( $max_filesize_bytes, 2 ) );
      }
      //validate image dimensions
      if ( $valid['is_valid'] && is_array( $dims ) && isset( $field['uprules_dims_ruletype'] ) && in_array( $field['uprules_dims_ruletype'], array( 'exact', 'conditional' ) ) ) {
        list( $up_width, $up_height ) = $dims;
        $valid = self::validate_image_dimensions( $field, $up_width, $up_height );
      }

      if ( ! $valid['is_valid'] )
        unset( RGFormsModel::$uploaded_files[$form_id][$input_name], $_FILES[$input_name] );

    }
    return $valid;
  }

  public function is_valid_dim( $val ) {
    return (bool)( trim($val) == absint( $val ) && $val > 0 );
  }

  public function validate_image_dimensions( $field, $width, $height ) {

    $valid = array( 'is_valid' => true, 'message' => '' );
    switch ( $field['uprules_dims_ruletype'] ) {
      case 'exact':
        if (
          isset( $field['uprules_dims_exact_width'] ) && self::is_valid_dim( $field['uprules_dims_exact_width'] ) && trim( $field['uprules_dims_exact_width'] ) != $width
          || isset( $field['uprules_dims_exact_height'] ) && self::is_valid_dim( $field['uprules_dims_exact_height'] ) && trim( $field['uprules_dims_exact_height'] ) != $height
        )
          $valid['is_valid'] = false;
          $valid['message'] = sprintf( __("Only images with dimensions %d&times;%d allowed.", "gforms_uprules"), $field['uprules_dims_exact_width'], $field['uprules_dims_exact_height'] );
        break;
      case 'conditional':
        $errors = "";

        if ( isset( $field['uprules_dims_minwidth'] ) && self::is_valid_dim( $field['uprules_dims_minwidth'] ) && trim( $field['uprules_dims_minwidth'] ) > $width ) {
          $valid['is_valid'] = false;
          $errors .= '<br/>' . sprintf( __("Min width: <b>%dpx</b>", "gforms_uprules"), $field['uprules_dims_minwidth'] );
        }
        if ( isset( $field['uprules_dims_minheight'] ) && self::is_valid_dim( $field['uprules_dims_minheight'] ) && trim( $field['uprules_dims_minheight'] ) > $height ) {
          $valid['is_valid'] = false;
          $errors .= '<br/>' . sprintf( __("Min height: <b>%dpx</b>", "gforms_uprules"), $field['uprules_dims_minheight'] );
        }
        if ( isset( $field['uprules_dims_maxwidth'] ) && self::is_valid_dim( $field['uprules_dims_maxwidth'] ) && trim( $field['uprules_dims_maxwidth'] ) < $width ) {
          $valid['is_valid'] = false;
          $errors .= '<br/>' . sprintf( __("Max width: <b>%dpx</b>", "gforms_uprules"), $field['uprules_dims_maxwidth'] );
        }
        if ( isset( $field['uprules_dims_maxheight'] ) && self::is_valid_dim( $field['uprules_dims_maxheight'] ) && trim( $field['uprules_dims_maxheight'] ) < $height ) {
          $valid['is_valid'] = false;
          $errors .= '<br/>' . sprintf( __("Max height: <b>%dpx</b>", "gforms_uprules"), $field['uprules_dims_maxheight'] );
        }

        if ( ! $valid['is_valid'] && ! empty( $errors ) )
          $valid['message'] = __("Image dimension didn't meet following size conditions:", "gforms_uprules") . $errors;

        break;
    }
    return $valid;
  }

  public function tooltips( $gf_tooltips ) {
    $gf_uprules_tooltips = array(
      'form_field_uprules_filesize' => "<h6>" . __("Filesize Limit", "gforms_uprules") . "</h6>" . __("Enter filesize limit for uploaded file. Exceeding uploads will be rejected with an error.", "gforms_uprules"),
      'form_field_uprules_dimensions' => "<h6>" . __("Image dimensions", "gforms_uprules") . "</h6>" . __("Set validation conditions for uploaded image. Choose between <i>Exact</i> or <i>Conditional</i> validation methods. Empty fields will be not checked against. All values are in <b>pixels</b>. Non-matching images will be rejected with an error.", "gforms_uprules")
    );

    return array_merge( $gf_uprules_tooltips, $gf_tooltips );
  }

	public function actions() {
    add_filter( 'gform_field_validation', array( __CLASS__, 'field_validation' ), 10, 4 );
		add_action( 'gform_field_advanced_settings', array( __CLASS__, 'field_settings' ), 5 );
		add_action( 'gform_editor_js', array( __CLASS__, 'editor_js' ), 20 );
		add_filter( 'gform_tooltips', array( __CLASS__, 'tooltips' ) );
		add_action( 'admin_init',  array( __CLASS__, 'register_scripts' ) );
		add_action( 'init', array( __CLASS__, 'localize' ) );
	}

}

GFUploadRules::actions();
