<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_email_classes', 'focoe_custom_woocommerce_emails' );

function focoe_custom_woocommerce_emails( $email_classes ) {

	//* Custom welcome email to customer when purchasing online training program

	
	require_once( __DIR__.'/class-wc-email-customer-cancelled-order.php' );

	//var_dump($email_classes);
	$email_classes['customer_cancelled_order'] = new WC_Email_Customer_Cancelled_Order(); // add to the list of email classes that WooCommerce loads


	return $email_classes;
	
}

?>