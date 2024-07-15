<?php 
if(is_author()) {
	global $author_id;
	$rating_id = $author_id;
} else {
	$rating_id = get_the_ID();
}
$total_ratings = get_post_meta($rating_id, 'houzez_total_rating', true); 

if( empty( $total_ratings ) ) {
	$total_ratings = 0;
}
?>

<div class="rating-score-wrap flex-grow-1">
	<div class="d-flex">
	    <?php if(is_singular( array('houzez_agent', 'houzez_agency') ) && $total_ratings != 0) { ?>
	        <span class="rating-score-text"><?php echo esc_attr(round($total_ratings, 2)); ?></span>
	    <?php } else if(is_author() && $total_ratings != 0 ) { ?>
	    	<span class="rating-score-text"><?php echo esc_attr(round($total_ratings, 2)); ?></span>
	    <?php } ?>

	    <div class="stars">
		    <?php echo houzez_get_stars($total_ratings, false); ?>
		</div>
	    
	    <?php if(is_singular( array('houzez_agent', 'houzez_agency') ) || is_author() ) { ?>
	        <a class="all-reviews" href="#review-scroll"><?php echo houzez_option('agency_lb_all_reviews', esc_html__('See all reviews', 'houzez')); ?></a>
	    <?php } ?>
	</div>
</div><!-- rating-score-wrap -->