<?php
$create_lisiting_enable = houzez_option('create_lisiting_enable');
$header_create_listing_template = houzez_get_template_link_2('template/user_dashboard_submit.php');
$favorite_template = houzez_get_template_link_2('template/user_dashboard_favorites.php');
$create_listing_button_required_login = houzez_option('create_listing_button');
$login_resgiter_type = houzez_option('login_register_type', 'as_icon');

$create_listing_title = houzez_option('dsh_create_listing', 'Create a Listing');
$add_to_favorite = houzez_option('add_to_favorite', 0);

$custom_create_lisiting_btn = houzez_option('custom_create_lisiting_btn', 0);
$custom_create_lisiting_link = houzez_option('custom_create_lisiting_link');
$custom_create_lisiting_title = houzez_option('custom_create_lisiting_title');

if( $custom_create_lisiting_btn && !empty($custom_create_lisiting_link) ) {
    $header_create_listing_template = $custom_create_lisiting_link;
    $create_listing_title = !empty($custom_create_lisiting_title) ? $custom_create_lisiting_title : $create_listing_title;
}
?>
<div class="login-register on-hover-menu">
	<ul class="login-register-nav dropdown d-flex align-items-center">

		<?php get_template_part('template-parts/header/partials/phone-header-1-4'); ?>
		
		<?php if( class_exists('Houzez_login_register') ): ?>

			<?php 
			if( ( houzez_option('header_login') || houzez_option('header_register') ) && !is_user_logged_in() && !houzez_is_login_page() ) { 

				if( $login_resgiter_type == 'as_text' ) { ?>
					
					<?php if( houzez_option('header_login') ) { ?>
					<li class="login-link">
						<a href="#" data-toggle="modal" data-target="#login-register-form"><?php esc_html_e('Login', 'houzez'); ?></a>
					</li>
					<?php } ?>

					<?php if( houzez_option('header_register') ) { ?>
					<li class="register-link">
						<a href="#" data-toggle="modal" data-target="#login-register-form"><?php esc_html_e('Register', 'houzez'); ?></a>
					</li>
					<?php } ?>

					<?php if( ! $add_to_favorite ) { ?>
					<li class="favorite-link">
						<a class="favorite-btn" href="<?php echo esc_url($favorite_template); ?>"><?php echo houzez_option('dsh_favorite', 'Favorites'); ?>  <span class="btn-bubble frvt-count">0</span></a>
					</li>
					<?php } ?>
				
				<?php 
				} else { ?>
					<li class="nav-item login-link">
						<a class="btn btn-icon-login-register" href="#" data-toggle="modal" data-target="#login-register-form"><i class="houzez-icon icon-single-neutral-circle"></i></a>
						<?php if( ! $add_to_favorite ) { ?>
						<ul class="dropdown-menu">
							<li class="nav-item">
								<a class="favorite-btn dropdown-item" href="<?php echo esc_url($favorite_template); ?>"><i class="houzez-icon icon-love-it mr-2"></i> <?php echo houzez_option('dsh_favorite', 'Favorites'); ?> <span class="btn-bubble frvt-count">0</span></a>
							</li>
						</ul>
						<?php } ?>
					</li>
				<?php } 
			} ?>

		<?php endif; ?>

		<?php if( $create_lisiting_enable != 0 ): ?>
		<li>
			<?php
            if( !empty($header_create_listing_template) ) {
                echo '<a href="'.esc_url( $header_create_listing_template ).'" class="btn btn-create-listing hidden-xs hidden-sm">'.esc_attr($create_listing_title).'</a>';
            }
            ?>
		</li>
		<?php endif; ?>

	</ul>
</div>