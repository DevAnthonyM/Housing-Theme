<?php
global $houzez_local, $agency_id;
$agency_id = get_the_ID();

$service_area = get_post_meta( $agency_id, 'fave_agent_service_area', true );
$agency_properties = Houzez_Query::agency_properties_count( $agency_id );

$agents_properties = 0;
$agency_agents_ids = Houzez_Query::loop_agency_agents_ids($agency_id);

if (!empty($agency_agents_ids)) {
    $agents_properties = Houzez_Query::get_agency_agents_properties_count($agency_agents_ids);
}

$properties = $agency_properties + $agents_properties;
?>
<div class="agency-grid-wrap">	
	<div class="agency-grid-image-wrap">
		<a class="agency-grid-image" href="<?php the_permalink($agency_id); ?>">
			<?php get_template_part('template-parts/realtors/agency/image'); ?>
		</a>
		<h2><a href="<?php the_permalink($agency_id); ?>"><?php echo get_the_title($agency_id); ?></a></h2>
		<?php 
        if( houzez_option( 'agency_review', 0 ) != 0 ) {
            get_template_part('template-parts/realtors/rating','v2'); 
        }?>
	</div><!-- agency-list-image -->
	<div class="agency-grid-content-wrap">
		<ul class="agency-list-contact list-unstyled">
			<?php if( ! empty($properties) ) { ?>
			<li><?php echo houzez_option('agency_lb_properties', esc_html__( 'Properties', 'houzez' )); ?>: <strong><?php echo esc_attr($properties); ?></strong></li>
			<?php } ?>
			<?php
			if( !empty( $service_area ) ) { ?>
				<li><?php echo houzez_option('agency_lb_service_areas', esc_html__( 'Service Areas', 'houzez' )); ?>:
					<strong>
					<?php echo esc_attr( $service_area ); ?></strong> 
				</li>
			<?php } ?>
		</ul><!-- agency-list-contact -->
		<a class="btn btn-primary-outlined btn-full-width" href="<?php the_permalink($agency_id); ?>">
			<?php echo houzez_option('agency_view_profile', esc_html__( 'View Profile', 'houzez' )); ?></a>
	</div><!-- agency-list-content -->
</div><!-- agency-list-wrap -->