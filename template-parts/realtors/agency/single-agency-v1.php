<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 29/09/16
 * Time: 5:10 PM
 * Since v1.4.0
 */
get_header();

global $post, $houzez_local, $properties_ids;

$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
if( $sticky_sidebar['agency_sidebar'] != 0 ) { 
    $is_sticky = 'houzez_sticky'; 
}

$properties_ids_array = array();
$listing_view = houzez_option('agency_listings_layout');
$agency_phone = get_post_meta( get_the_ID(), 'fave_agency_phone', true );
$agency_phone_call = str_replace(array('(',')',' ','-'),'', $agency_phone);

$item_layout = $view_class = $cols_in_row = '';
$card_deck = 'card-deck';

if($listing_view == 'list-view-v1') {
    $wrap_class = 'listing-v1';
    $item_layout = 'v1';
    $view_class = 'list-view';

} elseif($listing_view == 'grid-view-v1') {
    $wrap_class = 'listing-v1';
    $item_layout = 'v1';
    $view_class = 'grid-view';

} elseif($listing_view == 'list-view-v2') {
    $wrap_class = 'listing-v2';
    $item_layout = 'v2';
    $view_class = 'list-view';

} elseif($listing_view == 'grid-view-v2') {
    $wrap_class = 'listing-v2';
    $item_layout = 'v2';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v3') {
    $wrap_class = 'listing-v3';
    $item_layout = 'v3';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v4') {
    $wrap_class = 'listing-v4';
    $item_layout = 'v4';
    $view_class = 'grid-view';

} elseif($listing_view == 'list-view-v5') {
    $wrap_class = 'listing-v5';
    $item_layout = 'v5';
    $view_class = 'list-view';

} elseif($listing_view == 'grid-view-v5') {
    $wrap_class = 'listing-v5';
    $item_layout = 'v5';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v6') {
    $wrap_class = 'listing-v6';
    $item_layout = 'v6';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v7') {
    $wrap_class = 'listing-v7';
    $item_layout = 'v7';
    $view_class = 'grid-view';

} elseif($listing_view == 'list-view-v7') {
    $wrap_class = 'listing-v7';
    $item_layout = 'list-v7';
    $view_class = 'list-view';
    $card_deck = '';
} else {
    $wrap_class = 'listing-v1';
    $item_layout = 'v1';
    $view_class = 'grid-view';
}

$active_reviews_tab = $active_agents_tab = '';
$active_reviews_content = $active_agents_content = '';
if( houzez_option( 'agency_listings', 0 ) != 1 && houzez_option( 'agency_agents', 0 ) != 1 && houzez_option( 'agency_review', 0 ) != 0 ) {
    $active_reviews_tab = 'active';
    $active_reviews_content = 'show active';

} elseif( houzez_option( 'agency_listings', 0 ) == 0 && houzez_option( 'agency_agents', 0 ) == 1 ) {
    $active_agents_tab = 'active';
    $active_agents_content = 'show active';

} else {
    $active_listings_tab = 'active';
    $active_listings_content = 'show active';
}

if(isset($_GET['tab']) || $paged > 0) {

    if(isset($_GET['tab']) && $_GET['tab'] == 'reviews') {
        $active_reviews_tab = 'active';
        $active_reviews_content = 'show active';
        $active_listings_tab = '';
        $active_listings_content = '';
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('html, body').animate({
                scrollTop: $(".agent-nav-wrap").offset().top
            }, 'slow');
        });
    </script>
    <?php
}


global $paged;
if ( is_front_page()  ) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$agency_id = get_the_ID();

$tax_query = array();
$meta_query = array();

if ( isset( $_GET['tab'] ) && !empty($_GET['tab']) && $_GET['tab'] != "reviews") {
    $tax_query[] = array(
        'taxonomy' => 'property_status',
        'field' => 'slug',
        'terms' => $_GET['tab']
    );
}

$args = array(
    'post_type' => 'property',
    'posts_per_page' => houzez_option('num_of_agency_listings', 9),
    'paged' => $paged,
    'post_status' => 'publish',
);

$agents_array = array();
$agency_properties_ids = array();
$agents_properties_ids = array();

$agency_agents_ids = Houzez_Query::loop_agency_agents_ids(get_the_ID());

$agency_properties_ids = Houzez_Query::get_property_ids_by_agency(get_the_ID());

if (!empty($agency_agents_ids)) {
    $agents_properties_ids = Houzez_Query::get_property_ids_by_agents($agency_agents_ids);
}

$properties_ids = array_merge( $agency_properties_ids, $agents_properties_ids );
$properties_ids = array_unique( $properties_ids );


if (!empty($properties_ids)) {
    $args['post__in'] = $properties_ids;
} else {
    $args['post__in'] = array(-1); // To return no results if no properties are found.
}


$tax_count = count($tax_query);
if($tax_count > 0 ) {
    $args['tax_query'] = $tax_query;
}


$args = houzez_prop_sort($args);

$agency_qry = new WP_Query( $args );
$agency_total_listing = $agency_qry->found_posts;

$agents_query = Houzez_Query::loop_agency_agents(get_the_ID());


$content_classes = 'col-lg-8 col-md-12 bt-content-wrap';
if( houzez_option( 'agency_sidebar', 0 ) == 0 ) { 
    $content_classes = 'col-lg-12 col-md-12';
}
?>

<section class="content-wrap">
    <div class="container">

        <div class="agent-profile-wrap">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <div class="agent-image">
                        <?php get_template_part('template-parts/realtors/agency/image'); ?>
                    </div>
                </div>

                <div class="col-lg-9 col-md-9 col-sm-12">
                    <div class="agent-profile-top-wrap">
                        <div class="agent-profile-header">
                            <h1><?php the_title(); ?></h1>
                            <?php 
                            if( houzez_option( 'agency_review', 0 ) != 0 ) {
                                get_template_part('template-parts/realtors/rating'); 
                            }?>
                        </div>
                        <?php get_template_part('template-parts/realtors/agency/address'); ?>
                    </div>

                    <div class="agent-profile-content">
                        <ul class="list-unstyled">
                            <?php get_template_part('template-parts/realtors/agency/license'); ?>
                            <?php get_template_part('template-parts/realtors/agency/tax-number'); ?>
                        </ul>
                    </div><!-- agent-profile-content -->
                    <div class="agent-profile-buttons">

                        <?php if( houzez_option('agency_form_agency_page', 1) ) { ?>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#realtor-form">
                            <?php echo houzez_option('agency_send_email', esc_html__('Send Email', 'houzez')); ?>
                        </button>
                        <?php } ?>

                        <?php if(!empty($agency_phone)) { ?>
                        <a href="tel:<?php echo esc_attr($agency_phone_call); ?>" class="btn btn-call">
                            <span class="hide-on-click"><?php echo houzez_option('agency_lb_call', esc_html__('Call', 'houzez')); ?></span>
                            <span class="show-on-click"><?php echo esc_attr($agency_phone); ?></span>
                        </a>
                        <?php } ?>
                    </div><!-- agent-profile-buttons -->
                </div><!-- col-lg-8 col-md-8 col-sm-12 -->
            </div><!-- row -->
        </div><!-- agent-profile-wrap -->

        <?php if(!empty($properties_ids) && houzez_option('agency_stats', 0) != 0 ) { ?>
        <div class="agent-stats-wrap">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <?php get_template_part('template-parts/realtors/agency/stats-property-types'); ?> 
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <?php get_template_part('template-parts/realtors/agency/stats-property-status'); ?> 
                </div>

                <?php if(taxonomy_exists('property_city')) { ?>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <?php get_template_part('template-parts/realtors/agency/stats-property-cities'); ?> 
                </div>
                <?php } ?>
            </div>
        </div><!-- agent-stats-wrap -->
        <?php } ?>

        <div class="row">
            <div class="<?php echo esc_attr($content_classes); ?>">

                <?php if( houzez_option('agency_bio', 0) != 0 ) { ?>
                <div class="agent-bio-wrap">
                    <h2><?php echo houzez_option('agency_lb_about', esc_html__('About', 'houzez')); ?> <?php the_title(); ?></h2>
                    <?php the_content(); ?>

                    <?php get_template_part('template-parts/realtors/agency/languages'); ?>
                </div><!-- agent-bio-wrap -->
                <?php } ?>
                
                <?php if( houzez_option( 'agency_listings', 0 ) != 0 || houzez_option( 'agency_review', 0 ) != 0 || houzez_option( 'agency_agents', 0 ) != 0 ) { ?>
                <div id="review-scroll" class="agent-nav-wrap">
                    <ul class="nav nav-pills nav-justified">
                       
                        <?php if( houzez_option( 'agency_listings', 0 ) != 0 ) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo esc_attr($active_listings_tab); ?>" href="#tab-properties" data-toggle="pill" role="tab">
                                <?php echo houzez_option('agency_lb_listings', esc_html__('Listings', 'houzez')); ?> (<?php echo esc_attr($agency_total_listing); ?>)
                            </a>
                        </li>
                        <?php } ?>

                        <?php if( houzez_option( 'agency_agents', 0 ) != 0 ) { ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo esc_attr($active_agents_tab); ?>" href="#tab-agents" data-toggle="pill" role="tab">
                                <?php echo houzez_option('agency_lb_agents', esc_html__('Agents', 'houzez')); ?> (<?php echo esc_attr($agents_query->found_posts); ?>)
                            </a>
                        </li>
                        <?php } ?>

                        <?php if( houzez_option( 'agency_review', 0 ) != 0 ) { ?>
                        <li class="nav-item">
                            <a class="nav-link hz-review-tab <?php echo esc_attr($active_reviews_tab); ?>" href="#tab-reviews" data-toggle="pill" role="tab">
                                <?php echo houzez_option('agency_lb_reviews', esc_html__('Reviews', 'houzez')); ?> (<?php echo houzez_reviews_count('review_agency_id'); ?>)
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div><!-- agent-nav-wrap -->
                
                <div class="tab-content" id="tab-content">
                    
                    <?php if( houzez_option( 'agency_listings', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_listings_content); ?>" id="tab-properties">
                        <div class="listing-tools-wrap">
                            <div class="d-flex align-items-center">
                                <div class="listing-tabs flex-grow-1">
                                    <?php get_template_part('template-parts/realtors/agency/listing-tabs'); ?> 
                                </div>
                                <?php get_template_part('template-parts/realtors/agency/listing-sort-by'); ?>   
                            </div>
                        </div>

                        <section class="listing-wrap <?php echo esc_attr($wrap_class); ?>">
                            <div class="listing-view <?php echo esc_attr($view_class).' '.esc_attr($card_deck); ?>">
                                <?php
                                if ( $agency_qry->have_posts() ) :
                                    while ( $agency_qry->have_posts() ) : $agency_qry->the_post();

                                        get_template_part('template-parts/listing/item', $item_layout);

                                    endwhile;
                                    wp_reset_postdata();
                                else:
                                    get_template_part('template-parts/listing/item', 'none');
                                endif;
                                ?> 
                            </div><!-- listing-view -->

                            <?php houzez_pagination( $agency_qry->max_num_pages ); ?>
                        </section>
                        
                    </div><!-- tab-pane -->
                    <?php } ?>

                    <?php if( houzez_option( 'agency_agents', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_agents_content); ?>" id="tab-agents">
                        <div class="agents-list-view">
                            <?php
                            if ( $agents_query->have_posts() ) :
                                while ( $agents_query->have_posts() ) : $agents_query->the_post();

                                    get_template_part('template-parts/realtors/agent/list');

                                endwhile;
                                wp_reset_postdata();
                            else:
                                get_template_part('template-parts/realtors/agent/none');
                            endif;
                            ?> 
                        </div><!-- listing-view -->
                    </div><!-- tab-pane -->
                    <?php } ?>

                    <?php if( houzez_option( 'agency_review', 0 ) != 0 ) { ?>
                    <div class="tab-pane fade <?php echo esc_attr($active_reviews_content); ?>" id="tab-reviews">
                        <?php get_template_part('template-parts/reviews/main'); ?> 
                    </div><!-- tab-pane -->
                    <?php } ?>

                </div><!-- tab-content -->
                <?php } ?>

            </div><!-- bt-content-wrap -->

            <?php if( houzez_option( 'agency_sidebar', 0 ) != 0 ) { ?>
            <div class="col-lg-4 col-md-12 bt-sidebar-wrap <?php echo esc_attr($is_sticky); ?>">
                <aside class="sidebar-wrap">
                    <?php get_template_part('template-parts/realtors/agency/agency-contacts'); ?> 
                    <?php 
                    if (is_active_sidebar('agency-sidebar')) {
                        dynamic_sidebar('agency-sidebar');
                    }
                    ?>
                </aside>
            </div><!-- bt-sidebar-wrap -->
            <?php } ?>

        </div><!-- row -->
    </div><!-- container -->
</section><!-- listing-wrap -->

<?php get_footer(); ?>