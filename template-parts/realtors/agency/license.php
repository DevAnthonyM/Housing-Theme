<?php 
global $houzez_local;
$agency_licenses = get_post_meta( get_the_ID(), 'fave_agency_licenses', true );

if( !empty( $agency_licenses ) ) { ?>
	<li>
		<strong><?php echo houzez_option('agency_lb_license', esc_html__( 'License', 'houzez' )); ?>:</strong> 
		<?php echo esc_attr( $agency_licenses ); ?>
	</li>
<?php } ?>