<?php
/**
 * Override parent functions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Edification_Hub
 */


add_action( 'init', 'edification_hub_remove_parent_action');
function edification_hub_remove_parent_action() {
     remove_action( 'education_hub_action_after_header', 'education_hub_add_primary_navigation', 20 );
};

if ( ! function_exists( 'education_hub_add_primary_navigation' ) ) :

	/**
	 * Site branding.
	 *
	 * @since 1.0.0
	 */
	function education_hub_add_primary_navigation() {
	?>
    <div id="main-nav">
        <div class="main-nav-container">
	        <nav id="site-navigation" class="main-navigation" role="navigation">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<i class="fa fa-bars"></i>
					<i class="fa fa-close"></i>
					<span class="menu-label"><?php esc_html_e( 'Menu', 'edification-hub' ); ?></span>
				</button>
	            <div class="wrap-menu-content">
					<?php
					wp_nav_menu(
						array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'fallback_cb'    => 'education_hub_primary_menu_fallback',
						)
					);
					?>
					<div class="main-nav-search">
						<?php get_search_form(); ?>
					</div><!-- .main-nav-search -->
	            </div><!-- .menu-content -->
	        </nav><!-- #site-navigation -->

	        <?php $show_search_form = education_hub_get_option( 'show_search_form' ); ?>
		    <?php if ( true === $show_search_form ) : ?>
		    	<div class="primary-search-wrapper">
		    		<button class="search-toggle">
		    			<i class="fa fa-search"></i>
		    			<i class="fa fa-close"></i>
		    			<span class="screen-reader-text"><?php esc_html_e( 'Search', 'edification-hub' ); ?></span>
		    		</button>
				    <div id="primary-search-form" class="search-section search-box-wrap">
				    	<?php get_search_form(); ?>
				    </div><!-- .search-section -->
			    </div><!-- .primary-search-wrapper -->
		    <?php endif; ?>
       </div> <!-- .main-nav-container -->
    </div> <!-- #main-nav -->
    <?php
	}

endif;

if ( ! function_exists( 'education_hub_site_branding' ) ) :

	/**
	 * Site branding.
	 *
	 * @since 1.0.0
	 */
	function education_hub_site_branding() {

	?>
	    <div class="site-branding">

		    <?php education_hub_the_custom_logo(); ?>

			<?php $show_title = education_hub_get_option( 'show_title' ); ?>
			<?php $show_tagline = education_hub_get_option( 'show_tagline' ); ?>
			<?php if ( true === $show_title || true === $show_tagline ) :  ?>
	        <div id="site-identity">
				<?php if ( true === $show_title ) :  ?>
	            <?php if ( is_front_page() && is_home() ) : ?>
	              <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
	            <?php else : ?>
	              <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
	            <?php endif; ?>
				<?php endif ?>

				<?php if ( true === $show_tagline ) :  ?>
	            <p class="site-description"><?php bloginfo( 'description' ); ?></p>
				<?php endif; ?>
	        </div><!-- #site-identity -->
			<?php endif; ?>

	    </div><!-- .site-branding -->

	    <?php education_hub_add_primary_navigation();
	}
endif;
