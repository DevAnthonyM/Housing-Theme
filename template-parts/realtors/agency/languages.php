<?php 
global $houzez_local;
$languages = get_post_meta( get_the_ID(), 'fave_agency_language', true );

if( !empty( $languages ) ) { ?>
	<p>
		<i class="houzez-icon icon-messages-bubble mr-1"></i>
		<strong><?php echo houzez_option('agency_lb_language', esc_html__( 'Language', 'houzez' )); ?>:</strong> 
		<?php echo esc_attr( $languages ); ?>
	</p>
<?php } ?>