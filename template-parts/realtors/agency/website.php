<?php 
$website = get_post_meta( get_the_ID(), 'fave_agency_web', true );

if( !empty( $website ) ) { ?>
	<li>
		<strong><?php echo houzez_option('agency_lb_website', esc_html__('Website', 'houzez')); ?></strong> 
		<a target="_blank" href="<?php echo esc_url($website); ?>"><?php echo esc_attr( $website ); ?></a>
	</li>
<?php } ?>