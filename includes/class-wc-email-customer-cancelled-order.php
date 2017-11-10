<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Email_Customer_Cancelled_Order', false ) ) :

/**
 * Cancelled Order Email to customer.
 *
 * An email sent to the customer when an order is cancelled.
 *
 * @class       WC_Email_Customer_Cancelled_Order
 * @version     0.5.0
 * @package     woocommerce-failed-or-cancelled-order-email/includes/
 * @author      Sampo Virmasalo
 * @extends     WC_Email
 */
class WC_Email_Customer_Cancelled_Order extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'customer_cancelled_order';
		$this->customer_email = true;
		$this->title          = __( 'Cancelled order (customer)', 'wc-focoe' );
		$this->description    = __( 'Cancelled order emails are sent to customer when orders have been marked cancelled (if they were previously processing or on-hold).', 'wc-focoe' );
		$this->template_html    = 'emails/admin-cancelled-order.php';
		$this->template_plain   = 'emails/plain/admin-cancelled-order.php';

		// Triggers for this email
		/*add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
		add_action( 'woocommerce_order_status_on-hold_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
		add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
		add_action( 'woocommerce_order_status_failed_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );
		add_action( 'woocommerce_order_status_pending_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );*/
		add_action( 'woocommerce_order_status_processing_to_cancelled_notification', array( $this, 'trigger' ), 10, 2 );

		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int $order_id The order ID.
	 * @param WC_Order $order Order object.
	 */
	public function trigger( $order_id, $order = false ) {
		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_a( $order, 'WC_Order' ) ) {
			$this->object                  = $order;
			$this->recipient               = $this->object->get_billing_email();

			$this->find['order-date']      = '{order_date}';
			$this->find['order-number']    = '{order_number}';

			$this->replace['order-date']   = wc_format_datetime( $this->object->get_date_created() );
			$this->replace['order-number'] = $this->object->get_order_number();
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->setup_locale();
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		$this->restore_locale();

		$this->object->add_order_note( sprintf( __( '%s email sent to the customer.', 'woocommerce' ), $this->title ) );
		update_post_meta( $this->object->id, '_focoe_cancelled_email_sent', 1 );
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Your {site_title} order from {order_date} is complete', 'wc-focoe' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Your order is complete', 'wc-focoe' );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this,
		) );
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this,
		) );
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'         => __( 'Enable/Disable', 'wc-focoe' ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', 'wc-focoe' ),
				'default'       => 'yes',
			),
			'subject' => array(
				'title'         => __( 'Subject', 'wc-focoe' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'wc-focoe' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
				'placeholder'   => $this->get_default_subject(),
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email heading', 'wc-focoe' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'wc-focoe' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
				'placeholder'   => $this->get_default_heading(),
				'default'       => '',
			),
			'email_type' => array(
				'title'         => __( 'Email type', 'wc-focoe' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'wc-focoe' ),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'       => $this->get_email_type_options(),
				'desc_tip'      => true,
			),
		);
	}
}


endif;

return new WC_Email_Customer_Cancelled_Order();
