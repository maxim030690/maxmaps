<?php

function map( $atts ){

	$all_options = get_option('true_options');

	$map_view = 	'<div id="map_canvas"></div>
					<script>
					jQuery("#map_canvas").css("width", \''.$all_options["width"].'%\');
					jQuery("#map_canvas").css("height", '.$all_options["height"].');
					 
					var myLatlng = new google.maps.LatLng('.$all_options["longtitude"].', '.$all_options["latitude"].');
					var myOptions = {
						zoom: '.$all_options["zoom"].',
						center: myLatlng,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);';
	 
	$args = array( 'post_type' => 'markers', 'posts_per_page' => -1 );
                    $loop = new WP_Query( $args );
                    $num = 1;
                    while ( $loop->have_posts() ) : $loop->the_post();
                    $post_id = get_the_ID();
                    $longtitude_value = get_post_meta( $post_id, 'longtitude', true );
                    $latitude_value = get_post_meta( $post_id, 'latitude', true );

                    $map_view.='var infowindow'.$num.' = new google.maps.InfoWindow({
			          content: \''.get_the_title().'\'
			        });

					var marker'.$num.' = new google.maps.Marker({
						position: new google.maps.LatLng('.$longtitude_value.', '.$latitude_value.'),
						map: map,
						animation: google.maps.Animation.DROP
					});
					marker'.$num.'.addListener(\'click\', function() {
			          	infowindow'.$num.'.open(map, marker'.$num.');
			        });
					
			        jQuery("#markers_list' . $num . ' a").live("click", function(){		
			 
						infowindow'.$num.'.open(map, marker'.$num.');
						map.setZoom(12);
						map.setCenter(marker'.$num.'.position);
						});';
			        $num++;
                    endwhile;

	$map_view.='</script>';
	if($all_options["display_markers"] == 1){
	$map_view.='<table class="table table-bordered" style="margin-top: 30px;">
				    <thead>
				        <tr>
				            <th></th>
				            <th><h4>Location</h4></th>
				        </tr>
				    </thead>
				    <tbody>';

	$args1 = array( 'post_type' => 'markers', 'posts_per_page' => -1 );
                    $loop1 = new WP_Query( $args1 );
                    $num1 = 1;
                    while ( $loop1->have_posts() ) : $loop1->the_post();

	$map_view.=    '<tr>
			            <td style="width: 7%;padding-top: 15px;"><img src="'.MAX_MAPS_URL.'assets/img/marker.png" alt=""></td>
			            <td id="markers_list' . $num1 . '"><a href="#map_canvas" rel="' . $num1 . '">' .get_the_title(). '</a></td>
		        	</tr>';
		        	$num1++;
					endwhile;
	$map_view.=	'</tbody>
				</table>';
	}
	return $map_view;
}
add_shortcode('display_map', 'map');

add_action( 'wp_enqueue_scripts', 'my_scripts_for_map' );

function my_scripts_for_map(){
	$all_options = get_option('true_options');
	wp_enqueue_style( 'style_map', MAX_MAPS_URL.'assets/css/style.css' );
	wp_enqueue_script( 'mapscript', 'https://maps.googleapis.com/maps/api/js?key='.$all_options["api_key"].'');
	wp_enqueue_script( 'map_api_script', MAX_MAPS_URL.'assets/js/map.js');
}