<?php
$create_lisiting_enable = houzez_option('create_lisiting_enable');
$header_create_listing_template = houzez_get_template_link_2('template/user_dashboard_submit.php');
$create_listing_button_required_login = houzez_option('create_listing_button');

$create_listing_title = houzez_option('dsh_create_listing', 'Create a Listing');

$custom_create_lisiting_btn = houzez_option('custom_create_lisiting_btn', 0);
$custom_create_lisiting_link = houzez_option('custom_create_lisiting_link');
$custom_create_lisiting_title = houzez_option('custom_create_lisiting_title');

if( $custom_create_lisiting_btn && !empty($custom_create_lisiting_link) ) {
    $header_create_listing_template = $custom_create_lisiting_link;
    $create_listing_title = !empty($custom_create_lisiting_title) ? $custom_create_lisiting_title : $create_listing_title;
}
?>
<nav class="navi-login-register slideout-menu slideout-menu-right" id="navi-user">
	
	<?php if( $create_lisiting_enable != 0 ) { ?>
	<a class="btn btn-create-listing" href="<?php echo esc_url( $header_create_listing_template ); ?>"><?php echo esc_attr($create_listing_title); ?></a>
	<?php } ?>


    <?php if( class_exists('Houzez_login_register') && ( houzez_option('header_login') || houzez_option('header_register') ) ): ?>
	<ul class="logged-in-nav">
		
		<?php if( houzez_option('header_login')) { ?>
		<li class="login-link">
			<a href="#" data-toggle="modal" data-target="#login-register-form"><i class="houzez-icon icon-lock-5 mr-1"></i> <?php echo esc_html__('Login', 'houzez'); ?></a>
		</li><!-- .has-chil -->
		<?php } ?>

		<?php if( houzez_option('header_register')) { ?>
		<li class="register-link">
			<a href="#" data-toggle="modal" data-target="#login-register-form"><i class="houzez-icon icon-single-neutral-circle mr-1"></i> <?php echo esc_html__('Register', 'houzez'); ?></a>
		</li>
		<?php } ?>
		
	</ul><!-- .main-nav -->
	<?php endif; ?>
</nav><!-- .navi -->


