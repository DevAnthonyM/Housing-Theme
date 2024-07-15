<?php
$size = 'houzez-item-image-1';
$properties_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size='.$size, $post->ID );

$visible_images = houzez_option('luxury_gallery_visible', 12);
$images_in_row = houzez_option('luxury_gallery_columns', 4);;

if( empty($visible_images) ) {
    $visible_images = 9;
}

$percentage = 100 / $images_in_row;

if( !empty($properties_images) ) {

$total_images = count($properties_images);
$remaining_images = $total_images - $visible_images;
?>

<div class="fw-property-gallery-wrap fw-property-section-wrap" id="property-gallery-wrap">
	<div class="row row-no-padding">

		<?php 
		$i = 0;
		foreach( $properties_images as $prop_image_id => $prop_image_meta ) { $i++;
            $full_image = houzez_get_image_by_id( $prop_image_id, 'full' ); ?>

	        
				<a href="#" data-slider-no="<?php echo esc_attr($i); ?>" class="houzez-trigger-popup-slider-js gallery-grid-item swipebox hover-effect <?php if($i == $visible_images && $remaining_images > 0 ){ echo 'more-images'; } elseif($i > $visible_images) {echo 'gallery-hidden'; } ?>" data-toggle="modal" data-target="#property-lightbox">
					<?php if( $i == $visible_images && $remaining_images > 0 ){ echo '<span>'.$remaining_images.'+</span>'; } ?>
					<img class="img-fluid" src="<?php echo esc_url( $prop_image_meta['url'] ); ?>" width="<?php echo esc_attr( $prop_image_meta['width'] ); ?>" height="<?php echo esc_attr( $prop_image_meta['height'] ); ?>" alt="<?php echo esc_attr( $prop_image_meta['title'] ); ?>">
				</a>
			

	    <?php } ?>
	</div><!-- row -->
	<style> 
	    .fw-property-gallery-wrap .gallery-grid-item {
	        max-width: calc(<?php echo $percentage; ?>% - 1px);
	        margin-right: 1px;
	        margin-bottom: 1px;
	    }
	    .fw-property-gallery-wrap .more-images span {
		    top: 50%;
		    left: 50%;
		    text-align: center;
		    transform: translate(-50%, -50%);
		    color: #fff;
		    font-size: 45px;
		    font-weight: 300;
		    position: absolute;
		}
	</style>
</div><!-- fw-property-gallery-wrap -->
<?php } ?>