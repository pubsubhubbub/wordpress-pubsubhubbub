<div class="wrap">
	<h1><?php esc_html_e( 'WebSub', 'pubsubhubbub' ); ?> <small><?php esc_html_e( '(FKA. PubSubhubbub)', 'pubsubhubbub' ); ?></small></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'pubsubhubbub' ); ?>

		<h2 class="title"><?php esc_html_e( 'Publisher Settings', 'pubsubhubbub' ); ?></h2>

		<p><?php esc_html_e( 'A WebSub Publisher is an implementation that advertises a topic and hub URL on one or more resource URLs.', 'pubsubhubbub' ); ?></p>

		<?php
		// load the existing pubsub endpoint list from the WordPress options table
		$pubsubhubbub_endpoints = trim( implode( PHP_EOL, pubsubhubbub_get_hubs() ), PHP_EOL );
		?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Hubs <small>(one per line)</small>', 'pubsubhubbub' ); ?></th>
				<td>
					<textarea name="pubsubhubbub_endpoints" id="pubsubhubbub_endpoints" rows="10" cols="50" class="large-text"><?php echo $pubsubhubbub_endpoints; ?></textarea>
				</td>
			</tr>
		</table>

		<?php do_settings_fields( 'pubsubhubbub', 'publisher' ); ?>

		<?php do_settings_sections( 'pubsubhubbub' ); ?>

		<?php submit_button(); ?>
	</form>
</div>
