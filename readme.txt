=== WooCommerce Review Reminder ===
Contributors: Pixelix
Tags: woocommerce, review, reminder, email, напоминание, отзыв
Stable tag: 2.0.2
Requires at least: 3.5
Tested up to: 3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Remind the customer to leave a feedback about a purchase.

== Description ==

A week later after the order is marked as complete, the customer receives a reminder to leave feedback about a purchase.

== Installation ==

### en_US ###

1. Upload files to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.

* * *

### ru_RU ###

1. Загрузите файлы в папку "/wp-content/plugins/".
2. Активируйте плагин в меню "Плагины".

== Frequently Asked Questions ==

### en_US ###

= What is it needed for? =

To customers left more feedback and returned for new purchases.

* * *

### ru_RU ###

= Для чего это нужно? =

Чтобы покупатели оставляли больше отзывов и возвращались за новыми покупками.

== For developers ==

You can change the email content using the filters:

* `woocommerce_review_reminder_email_subject`
* `woocommerce_review_reminder_email_title`
* `woocommerce_review_reminder_email_message`

= Example how to of changing the subject =

	/**
	 * My custom email subject.
	 *
	 * @param  WC_Order $order Order data/object.
	 *
	 * @return string          New email subject.
	 */
	function my_wc_review_reminder_email_subject( $order ) {
		return 'My new subject';
	}

	add_filter( 'woocommerce_review_reminder_email_subject', 'my_wc_review_reminder_email_subject' );

= Example how to of changing the email title =

	/**
	 * My custom email title.
	 *
	 * @param  WC_Order $order Order data/object.
	 *
	 * @return string          New email title.
	 */
	function my_wc_review_reminder_email_title( $order ) {
		return 'My new title';
	}

	add_filter( 'woocommerce_review_reminder_email_title', 'my_wc_review_reminder_email_title' );

= Example how to of changing the email message =

	/**
	 * My custom email message.
	 *
	 * @param  string   $message      Default email message.
	 * @param  WC_Order $order Order  data/object.
	 * @param  string   $product_list Products list with links.
	 *
	 * @return string                 New email message.
	 */
	function my_wc_review_reminder_email_message( $message, $order, $product_list ) {
		return 'My new message';
	}

	add_filter( 'woocommerce_review_reminder_email_message', 'my_wc_review_reminder_email_message', 1, 3 );

== Screenshots ==

1. Plugin settings.
2. Example of the reminder.

== Changelog ==

= 2.0 =

* Improved all the code.
* Improved the notifications. Now the emails are sent with the WooCommerce email template.
* Improved the plugin settings. Removed some options in favor of the WooCommerce mailer.
* Entered the woocommerce_review_reminder_email_subject filter for change the email subject.
* Entered the woocommerce_review_reminder_email_title filter for change the email title.
* Entered the woocommerce_review_reminder_email_message filter for change the email message.

= 1.0 =

* Initial release.
* Стартовый релиз.

== Upgrade Notice ==

= 2.0 =

* Improved all the code.
* Improved the notifications. Now the emails are sent with the WooCommerce email template.
* Improved the plugin settings. Removed some options in favor of the WooCommerce mailer.
* Entered the woocommerce_review_reminder_email_subject filter for change the email subject.
* Entered the woocommerce_review_reminder_email_title filter for change the email title.
* Entered the woocommerce_review_reminder_email_message filter for change the email message.