<div class="wrap">
<h2><?php _e( 'Define custom hubs', 'pubsubhubbub' ); ?></h2>

<form method="post" action="options.php">
<!-- starting -->
<?php settings_fields( 'pubsubhubbub_options' ); ?>
<?php do_settings_sections( 'pubsubhubbub_options' ); ?>
<!-- ending -->

<?php
// load the existing pubsub endpoint list from the wordpress options table
$pubsubhubbub_endpoints = trim( implode( PHP_EOL, pubsubhubbub_get_hubs() ), PHP_EOL );
?>

<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php _e( 'Hubs (one per line)', 'pubsubhubbub' ); ?></th>
		<td><textarea name="pubsubhubbub_endpoints" rows="10" cols="50" class="large-text"><?php echo $pubsubhubbub_endpoints; ?></textarea></td>
	</tr>
</table>

<?php submit_button(); ?>

</form>

<p><strong><?php _e( 'Thanks for using WebSub/PubSubHubbub!', 'pubsubhubbub' ); ?></strong></p>
</div>
