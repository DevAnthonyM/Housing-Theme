<?php 
global $agency_id;
if( has_post_thumbnail($agency_id) ) {
	echo get_the_post_thumbnail($agency_id, 'medium_large', array('class' => 'img-fluid'));
} else { 
	houzez_image_placeholder( 'large' );
}
?>