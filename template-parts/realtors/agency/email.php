<?php 
global $houzez_local;
$agency_email = get_post_meta( get_the_ID(), 'fave_agency_email', true );

if( !empty( $agency_email ) ) { ?>
    <li class="email">
    	<strong><?php echo houzez_option('agency_lb_email', esc_html__( 'Email', 'houzez' )); ?></strong> 
    	<a href="mailto:<?php echo esc_attr( $agency_email ); ?>"><?php echo esc_attr( $agency_email ); ?></a>
    </li>
<?php } ?>