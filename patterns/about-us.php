<?php

/**
 * Title: About Us
 * Slug: mt-tickets/about-us
 * Categories: mt-tickets
 * Viewport Width: 1200
 */
?>
<!-- wp:group {"tagName":"section","style":{"spacing":{"padding":{"top":"var(--wp--preset--spacing--xl)","bottom":"var(--wp--preset--spacing--xl)"}},"color":{"background":"var(--wp--preset--color--surface)"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group has-background" style="background-color:var(--wp--preset--color--surface);padding-top:var(--wp--preset--spacing--xl);padding-bottom:var(--wp--preset--spacing--xl)">

	<!-- wp:columns {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}}} -->
	<div class="wp-block-columns" style="margin-top:0;margin-bottom:0">

		<!-- Left Column: Image -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"border":{"radius":"18px","width":"1px","color":"var(--wp--preset--color--border)"},"spacing":{"padding":{"top":"var(--wp--preset--spacing--m)","bottom":"var(--wp--preset--spacing--m)","left":"var(--wp--preset--spacing--m)","right":"var(--wp--preset--spacing--m)"}}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group" style="border-color:var(--wp--preset--color--border);border-width:1px;border-radius:18px;padding-top:var(--wp--preset--spacing--m);padding-right:var(--wp--preset--spacing--m);padding-bottom:var(--wp--preset--spacing--m);padding-left:var(--wp--preset--spacing--m)">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"14px"}}} -->
				<figure class="wp-block-image size-full" style="border-radius:14px"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/about-us.png')); ?>" alt="About Us" /></figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- Right Column: Content -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">

				<!-- Row 1: About Us label -->
				<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px","textTransform":"uppercase","letterSpacing":"0.5px"}},"textColor":"accent"} -->
				<p class="has-accent-color">About Us</p>
				<!-- /wp:paragraph -->

				<!-- Row 2: Main heading -->
				<!-- wp:heading {"level":2,"style":{"spacing":{"margin":{"top":"8px","bottom":"16px"}},"typography":{"fontSize":"32px","fontWeight":"700"}}} -->
				<h2 class="wp-block-heading" style="margin-top:8px;margin-bottom:16px;font-size:32px;font-weight:700">We make ticket booking simple and efficient for everyone.</h2>
				<!-- /wp:heading -->

				<!-- Row 3: Description -->
				<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"24px"}}}} -->
				<p style="margin-bottom:24px">Our goal is to provide a seamless experience for passengers and carriers alike. We connect travelers with reliable transportation options while giving carriers powerful tools to manage their operations.</p>
				<!-- /wp:paragraph -->

				<!-- Row 4: Feature block 1 -->
				<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"16px"},"padding":{"top":"16px","bottom":"16px","left":"0","right":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
				<div class="wp-block-group" style="margin-bottom:16px;padding-top:16px;padding-right:0;padding-bottom:16px;padding-left:0">
					<!-- wp:image {"width":48,"height":48,"sizeSlug":"full","linkDestination":"none","className":"mt-about-feature-icon"} -->
					<figure class="wp-block-image size-full is-resized mt-about-feature-icon"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/icon-booking.svg')); ?>" alt="" width="48" height="48" /></figure>
					<!-- /wp:image -->
					<!-- wp:group {"layout":{"type":"constrained"}} -->
					<div class="wp-block-group">
						<!-- wp:heading {"level":4,"style":{"spacing":{"margin":{"top":"0","bottom":"4px"}},"typography":{"fontSize":"18px","fontWeight":"600"}}} -->
						<h4 class="wp-block-heading" style="margin-top:0;margin-bottom:4px;font-size:18px;font-weight:600">User-Friendly Booking System</h4>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
						<p style="font-size:14px">Intuitive interface that makes booking tickets quick and easy for passengers.</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->

				<!-- Row 5: Feature block 2 -->
				<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"24px"},"padding":{"top":"16px","bottom":"16px","left":"0","right":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
				<div class="wp-block-group" style="margin-bottom:24px;padding-top:16px;padding-right:0;padding-bottom:16px;padding-left:0">
					<!-- wp:image {"width":48,"height":48,"sizeSlug":"full","linkDestination":"none","className":"mt-about-feature-icon"} -->
					<figure class="wp-block-image size-full is-resized mt-about-feature-icon"><img src="<?php echo esc_url(get_theme_file_uri('assets/images/icon-schedule.svg')); ?>" alt="" width="48" height="48" /></figure>
					<!-- /wp:image -->
					<!-- wp:group {"layout":{"type":"constrained"}} -->
					<div class="wp-block-group">
						<!-- wp:heading {"level":4,"style":{"spacing":{"margin":{"top":"0","bottom":"4px"}},"typography":{"fontSize":"18px","fontWeight":"600"}}} -->
						<h4 class="wp-block-heading" style="margin-top:0;margin-bottom:4px;font-size:18px;font-weight:600">Schedule Management</h4>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"style":{"typography":{"fontSize":"14px"}}} -->
						<p style="font-size:14px">Comprehensive tools for carriers to manage routes, schedules, and availability efficiently.</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->

				<!-- Row 6: Contact button -->
				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"url":"/contact-us/","backgroundColor":"primary","textColor":"background","style":{"spacing":{"padding":{"top":"12px","bottom":"12px","left":"24px","right":"24px"}},"typography":{"fontWeight":"600"}}} -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button has-primary-background-color has-background-color has-background-background-color has-text-color" href="/contact-us/" style="padding-top:12px;padding-right:24px;padding-bottom:12px;padding-left:24px;font-weight:600;background-color:var(--wp--preset--color--primary);color:#FFFFFF">Contact Us</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->

			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</section>
<!-- /wp:group -->