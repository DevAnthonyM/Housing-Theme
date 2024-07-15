<?php 
global $houzez_local;
$agency_tax_no = get_post_meta( get_the_ID(), 'fave_agency_tax_no', true );

if( !empty( $agency_tax_no ) ) { ?>
	<li>
		<strong><?php echo houzez_option('agency_lb_tax_number', esc_html__( 'Tax Number', 'houzez' )); ?>:</strong> 
		<?php echo esc_attr( $agency_tax_no ); ?>
	</li>
<?php } ?>