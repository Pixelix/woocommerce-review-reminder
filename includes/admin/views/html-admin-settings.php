<?php check_wcrr_options(); ?>
<div class="wrap">
	<h2>
		<?php echo get_admin_page_title(); ?>
	</h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'wcrr_options' ); ?>
		<?php do_settings_sections( 'wcrr_options' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<?php _e('Sender name', 'woocommerce-review-reminder'); ?>
				</th>
				<td>
					<input type="text" name="mailer_name" value="<?php echo get_option('mailer_name'); ?>" class="regular-text">
					<p class="description">
						<?php _e('The name of the sender will be used in the return address reminder letters.', 'woocommerce-review-reminder'); ?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Sender address', 'woocommerce-review-reminder'); ?>
				</th>
				<td>
					<input type="text" name="mailer_email" value="<?php echo get_option('mailer_email'); ?>" class="regular-text">
					<p class="description">
						<?php _e('E-mail address of the sender of the message reminder.', 'woocommerce-review-reminder'); ?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Delay of departure of the letter', 'woocommerce-review-reminder'); ?>
				</th>
				<td>
					<input type="text" name="interval_count" value="<?php echo get_option('interval_count'); ?>" class="small-text">
					<select name="interval_type">
						<option value="1" <?php if (get_option('interval_type') == '1'){echo 'selected=selected';} ?>><?php _e('seconds', 'woocommerce-review-reminder'); ?></option>
						<option value="60" <?php if (get_option('interval_type') == '60'){echo 'selected=selected';} ?>><?php _e('minutes', 'woocommerce-review-reminder'); ?></option>
						<option value="3600" <?php if (get_option('interval_type') == '3600'){echo 'selected=selected';} ?>><?php _e('hours', 'woocommerce-review-reminder'); ?></option>
						<option value="86400" <?php if (get_option('interval_type') == '86400'){echo 'selected=selected';} ?>><?php _e('days', 'woocommerce-review-reminder'); ?></option>
						<option value="604800" <?php if (get_option('interval_type') == '604800'){echo 'selected=selected';} ?>><?php _e('weeks', 'woocommerce-review-reminder'); ?></option>
						<option value="18144000" <?php if (get_option('interval_type') == '18144000'){echo 'selected=selected';} ?>><?php _e('months', 'woocommerce-review-reminder'); ?></option>
					</select>
					<p class="description">
						<?php _e('A reminder letter will be sent after the specified interval after the execution of the order.', 'woocommerce-review-reminder'); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
<div class="clear">
