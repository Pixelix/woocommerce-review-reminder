<?php
/**
 * WooCommerce Review Reminder admin settings view.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<?php settings_errors(); ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'wcrr_options' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Sender name', $this->plugin_slug ); ?>
				</th>
				<td>
					<input type="text" name="mailer_name" value="<?php echo get_option('mailer_name'); ?>" class="input-text regular-text" />
					<p class="description">
						<?php _e( 'The name of the sender will be used in the return address reminder letters.', $this->plugin_slug ); ?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Sender address', $this->plugin_slug ); ?>
				</th>
				<td>
					<input type="text" name="mailer_email" value="<?php echo get_option( 'mailer_email' ); ?>" class="input-text regular-text" />
					<p class="description">
						<?php _e( 'E-mail address of the sender of the message reminder.', $this->plugin_slug ); ?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Delay of departure of the letter', $this->plugin_slug ); ?>
				</th>
				<td>
					<input type="text" name="interval_count" value="<?php echo get_option( 'interval_count' ); ?>" class="input-text regular-text" style="width: 50px;" />
					<select name="interval_type">
						<?php $interval_type = get_option( 'interval_type' ); ?>
						<option value="1" <?php selected( '1', $interval_type, true ); ?>><?php _e( 'seconds', $this->plugin_slug ); ?></option>
						<option value="60" <?php selected( '60', $interval_type, true ); ?>><?php _e( 'minutes', $this->plugin_slug ); ?></option>
						<option value="3600" <?php selected( '3600', $interval_type, true ); ?>><?php _e( 'hours', $this->plugin_slug ); ?></option>
						<option value="86400" <?php selected( '86400', $interval_type, true ); ?>><?php _e( 'days', $this->plugin_slug ); ?></option>
						<option value="604800" <?php selected( '604800', $interval_type, true ); ?>><?php _e( 'weeks', $this->plugin_slug ); ?></option>
						<option value="18144000" <?php selected( '18144000', $interval_type, true ); ?>><?php _e( 'months', $this->plugin_slug ); ?></option>
					</select>
					<p class="description">
						<?php _e( 'A reminder letter will be sent after the specified interval after the execution of the order.', $this->plugin_slug ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
