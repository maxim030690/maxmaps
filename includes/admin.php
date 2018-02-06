<?php

add_action('admin_menu', function(){
	add_menu_page( 'Settings', 'Maxmaps', 'manage_options', 'max_maps_plugin_settings', '', '', 10 );
} );

add_action('admin_menu', 'register_my_custom_submenu_page');

function register_my_custom_submenu_page() {
	add_submenu_page( 'max_maps_plugin_settings', 'My markers', 'Settings', 'manage_options', 'my-custom-submenu-page', 'add_my_setting' ); 
}

add_action('init', 'create_post_type_markers');

function create_post_type_markers() { // создаем новый тип записи
	register_post_type( 'markers', // указываем названия типа
	array(
	'labels' => array( 
	'name' => __( 'markers' ), // даем названия разделу для панели управления
	'singular_name' => __( 'markers' ), // даем названия одной записи
	'add_new' => __('Add markers'),// далее полная русификация админ. панели
	'add_new_item' => __('Add new markers'),
	'edit_item' => __('Edit markers'),
	'new_item' => __('New markers'),
	'all_items' => __('My markers'),
	'view_item' => __('View markers'),
	'search_items' => __('Search markers'),
	'not_found' => __('No markers'),
	'not_found_in_trash' => __('Markers not found'), 
	'menu_name' => 'Markers',
	), 
	'public' => true,
	// 'menu_position' => 5,
	'show_in_menu'  => 'max_maps_plugin_settings',
	// 'show_ui'  => false,
	'rewrite' => array('slug' => 'markers'), // указываем slug для ссылок например: mysite/reviews/
	'supports' => array('title')
	) 
	); 
}

/* Добавляем блоки в основную колонку на страницах постов и пост. страниц */
function myplugin_add_custom_box() {
	$screens = array( 'markers');
	foreach ( $screens as $screen )
		add_meta_box( 'myplugin_sectionid', 'longitude and latitude', 'myplugin_meta_box_callback', $screen );
}
add_action('add_meta_boxes', 'myplugin_add_custom_box');

/* HTML код блока */
function myplugin_meta_box_callback() {
	$post_id = get_the_ID();
	$longtitude_value = get_post_meta( $post_id, 'longtitude', true );
	$latitude_value = get_post_meta( $post_id, 'latitude', true );
	// Используем nonce для верификации
	wp_nonce_field( plugin_basename(__FILE__), 'max_map_plugin' );


	echo '<input type="text" value="'.$longtitude_value.'" id= "longtitude" name="longtitude" placeholder="longtitude" size="25" /></br></br>';

	echo '<input type="text" value="'.$latitude_value.'" id= "latitude" name="latitude" placeholder="latitude" size="25" />';
}

/* Сохраняем данные, когда пост сохраняется */
function myplugin_save_postdata( $post_id ) {
	// проверяем nonce нашей страницы, потому что save_post может быть вызван с другого места.
	if ( ! wp_verify_nonce( $_POST['max_map_plugin'], plugin_basename(__FILE__) ) )
		return $post_id;

	// проверяем, если это автосохранение ничего не делаем с данными нашей формы.
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;

	// проверяем разрешено ли пользователю указывать эти данные
	if ( 'markers' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
		  return $post_id;
	} elseif( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Убедимся что поле установлено.
	if ( ! isset( $_POST['longtitude'] ) )
		return;

	// Все ОК. Теперь, нужно найти и сохранить данные
	// Очищаем значение поля input.
	$longtitude =  $_POST['longtitude'] ;
	$latitude = $_POST['latitude'] ;

	// Обновляем данные в базе данных.
	update_post_meta( $post_id, 'longtitude', $longtitude );
	update_post_meta( $post_id, 'latitude', $latitude );
}
add_action( 'save_post', 'myplugin_save_postdata' );

function add_my_setting(){
	$true_page = 'max_maps_plugin_settings';
	?>
	<div class="wrap">
		<form action="options.php" method="POST">
			<?php 
			settings_fields('max_map_options');
			do_settings_sections($true_page);?>
			<?php submit_button();
			?>	
		</form>
		<?php echo '<h3>Shortcode: [display_map]</h3>'; ?>
	</div>
	<?php

}

function true_option_settings() {
	$true_page = 'max_maps_plugin_settings';
	
	register_setting( 'max_map_options', 'true_options', 'true_validate_settings' ); 
 
	
	add_settings_section( 'true_section_1', 'Settings map', '', $true_page );
 
	$true_field_params = array(
		'type'      => 'text', 
		'id'        => 'api_key',
		'desc'      => 'Enter your api key'
	);
	add_settings_field( 'api_key_field', 'Api key', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );

	$true_field_params = array(
		'type'      => 'text', 
		'id'        => 'width',
		'desc'      => 'width map'
	);
	add_settings_field( 'width_map_field', 'Width map in %', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );

	$true_field_params = array(
		'type'      => 'text', 
		'id'        => 'height',
		'desc'      => 'height map'
	);
	add_settings_field( 'height_map_field', 'Height map in px', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );
	$true_field_params = array(
		'type'      => 'text', 
		'id'        => 'zoom',
		'desc'      => 'example: 6 or 8 or 10'
	);
	add_settings_field( 'zoom_map_field', 'Zoom map', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );
	$true_field_params = array(
		'type'      => 'text', 
		'id'        => 'longtitude',
		'desc'      => 'longtitude'
	);
	add_settings_field( 'longtitude_map_field', 'longtitude map', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );
	$true_field_params = array(
		'type'      => 'text', 
		'id'        => 'latitude',
		'desc'      => 'latitude'
	);
	add_settings_field( 'latitude_map_field', 'latitude map', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );

	$true_field_params = array(
		'type'      => 'text', 
		'id'        => 'display_markers',
		'desc'      => 'Choose 1 if display'
	);
	add_settings_field( 'display_map_field', 'display markers', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );

}
add_action( 'admin_init', 'true_option_settings' );

function true_option_display_settings($args) {
	extract( $args );
 
	$option_name = 'true_options';
 
	$o = get_option( $option_name );
 
	switch ( $type ) {  
		case 'text':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<input class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
		break;
		case 'textarea':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<textarea class='code large-text' cols='50' rows='10' type='text' id='$id' name='" . $option_name . "[$id]'>$o[$id]</textarea>";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
		break;
		case 'checkbox':
			$checked = ($o[$id] == 'on') ? " checked='checked'" :  '';  
			echo "<label><input type='checkbox' id='$id' name='" . $option_name . "[$id]' $checked /> ";  
			echo ($desc != '') ? $desc : "";
			echo "</label>";  
		break;
		case 'select':
			echo "<select id='$id' name='" . $option_name . "[$id]'>";
			foreach($vals as $v=>$l){
				$selected = ($o[$id] == $v) ? "selected='selected'" : '';  
				echo "<option value='$v' $selected>$l</option>";
			}
			echo ($desc != '') ? $desc : "";
			echo "</select>";  
		break;
		case 'radio':
			echo "<fieldset>";
			foreach($vals as $v=>$l){
				$checked = ($o[$id] == $v) ? "checked='checked'" : '';  
				echo "<label><input type='radio' name='" . $option_name . "[$id]' value='$v' $checked />$l</label><br />";
			}
			echo "</fieldset>";  
		break; 
	}
}
 

function true_validate_settings($input) {
	foreach($input as $k => $v) {
		$valid_input[$k] = trim($v);
 
	}
	return $valid_input;
}