<?php
namespace app\Models;

class PayPal {
	public function __construct($config = "") {

		// default settings
		$settings = array (
				'business' => 'lauraowen3000@gmail.com', // paypal email address
				'currency' => 'GBP', // paypal currency
				'cursymbol' => '&pound;', // currency symbol
				'location' => 'GB', // location code (ex GB)
				'returnurl' => 'http://thepaperstash.co.uk/orders.php?action=paid', // where to go back when the transaction is done.
				'returntxt' => 'Return to The Paper Stash', // What is written on the return button in paypal
				'cancelurl' => 'http://thepaperstash.co.uk/cart_cancel.php', // Where to go if the user cancels.
				'shipping' => '2.95', // Shipping Cost
				'weight_unit' => 'kgs',
				'custom' => ''
		);

		// overrride default settings
		if (! empty ( $config )) {
			foreach ( $config as $key => $val ) {
				if (! empty ( $val )) {
					$settings [$key] = $val;
				}
			}
		}

		// Set the class attributes
		$this->business = $settings ['business'];
		$this->currency = $settings ['currency'];
		$this->cursymbol = $settings ['cursymbol'];
		$this->location = $settings ['location'];
		$this->returnurl = $settings ['returnurl'];
		$this->returntxt = $settings ['returntxt'];
		$this->cancelurl = $settings ['cancelurl'];
		$this->shipping = $settings ['shipping'];
		$this->custom = $settings ['shipping'];
		$this->weight = $settings['weight_unit'];
		$this->items = array();
	}
}