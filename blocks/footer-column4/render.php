<?php
if (!defined('ABSPATH')) exit;

$default_title = 'For Contact';
$title = get_option('mt_tickets_footer_column4_title', $default_title);

$default_description = "Address:\nPhone:\nEmail:\nOpening hours:";
$description = get_option('mt_tickets_footer_column4_description', $default_description);

$attrs = get_block_wrapper_attributes(array('class' => 'mt-footer-column4'));
?>
<div <?php echo $attrs; ?>>
	<?php if ($title) : ?>
		<h4 class="mt-footer-column4__title"><?php echo esc_html($title); ?></h4>
	<?php endif; ?>
	
	<?php if ($description) : ?>
		<div class="mt-footer-column4__description">
			<?php
			// Convert newlines to <br> tags for display
			$description_lines = explode("\n", $description);
			foreach ($description_lines as $line) {
				$line = trim($line);
				if ($line) {
					echo '<p>' . esc_html($line) . '</p>';
				}
			}
			?>
		</div>
	<?php endif; ?>
</div>
