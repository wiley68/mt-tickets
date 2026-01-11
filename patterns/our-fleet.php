<?php

/**
 * Title: Our Fleet
 * Slug: mt-tickets/our-fleet
 * Categories: mt-tickets
 * Viewport Width: 1200
 */
?>
<!-- wp:group {"tagName":"section","style":{"spacing":{"padding":{"top":"var(--wp--preset--spacing--xl)","bottom":"var(--wp--preset--spacing--xl)"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group" style="padding-top:var(--wp--preset--spacing--xl);padding-bottom:var(--wp--preset--spacing--xl)">

	<!-- wp:group {"layout":{"type":"constrained","contentSize":"760px"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"14px","textTransform":"uppercase","letterSpacing":"0.5px"}},"textColor":"accent"} -->
		<p class="has-text-align-center has-accent-color">Our fleet</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":2,"style":{"spacing":{"margin":{"top":"8px","bottom":"var(--wp--preset--spacing--l)"}},"typography":{"fontSize":"32px","fontWeight":"700"}}} -->
		<h2 class="wp-block-heading has-text-align-center" style="margin-top:8px;margin-bottom:var(--wp--preset--spacing--l);font-size:32px;font-weight:700">Explore our diverse fleet</h2>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->

	<!-- wp:woocommerce/product-collection {"queryId":1,"query":{"perPage":9,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","search":"","exclude":[],"inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","outofstock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[],"filterable":false,"relatedBy":{"categories":true,"tags":true}},"tagName":"div","displayLayout":{"type":"carousel","columns":5,"shrinkColumns":true},"dimensions":{"widthType":"fill"},"collection":"woocommerce/product-collection/by-category","hideControls":["inherit","hand-picked","filterable"],"queryContextIncludes":["collection"],"__privatePreviewState":{"isPreview":false,"previewMessage":"Please select a product category in the block settings to display products."}} -->
	<div class="wp-block-woocommerce-product-collection">
		<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
		<div class="wp-block-group">
			<!-- wp:woocommerce/product-gallery-large-image-next-previous {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-woocommerce-product-gallery-large-image-next-previous"></div>
			<!-- /wp:woocommerce/product-gallery-large-image-next-previous -->
		</div>
		<!-- /wp:group -->

		<!-- wp:woocommerce/product-template {"layout":{"type":"flex","justifyContent":"left","verticalAlignment":"top","flexWrap":"nowrap","orientation":"horizontal"}} -->
		<!-- wp:woocommerce/product-image {"showSaleBadge":false,"imageSizing":"thumbnail","isDescendentOfQueryLoop":true} -->
		<!-- wp:woocommerce/product-sale-badge {"align":"right"} /-->
		<!-- /wp:woocommerce/product-image -->

		<!-- wp:post-title {"textAlign":"center","isLink":true,"style":{"spacing":{"margin":{"bottom":"0.75rem","top":"0"}},"typography":{"lineHeight":"1.4"}},"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

		<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"center","fontSize":"small"} /-->

		<!-- wp:woocommerce/product-button {"textAlign":"center","isDescendentOfQueryLoop":true,"fontSize":"small"} /-->
		<!-- /wp:woocommerce/product-template -->
	</div>
	<!-- /wp:woocommerce/product-collection -->

</section>
<!-- /wp:group -->