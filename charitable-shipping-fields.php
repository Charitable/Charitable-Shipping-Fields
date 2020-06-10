<?php
/**
 * Plugin Name:       Charitable - Shipping Fields
 * Plugin URI:        https://github.com/Charitable/Charitable-Shipping-Fields/
 * Description:       Add a Shipping Fields section to your donation form.
 * Version:           0.1
 * Author:            WPCharitable
 * Author URI:        https://www.wpcharitable.com/
 * Requires at least: 5.2
 * Tested up to:      5.4.1
 *
 * Text Domain:       charitable-shipping-fields
 * Domain Path:       /languages/
 *
 * @package  Charitable Shipping Fields
 * @category Core
 * @author   Studio164a
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the Shipping Details section to the donation form.
 *
 * @param  array                    $fields The donation form fields.
 * @param  Charitable_Donation_Form $form   Instance of `Charitable_Donation_Form`.
 * @return array
 */
add_filter(
	'charitable_donation_form_fields',
	function( $fields, $form ) {
		$fields['shipping_fields'] = [
			'legend'   => 'Shipping Details',
			'type'     => 'fieldset',
			'fields'   => $form->get_sanitized_donation_fields( 'shipping-details' ),
			'priority' => 50,
		];

		return $fields;
	},
	10,
	2
);

/**
 * Register new donation fields.
 */
add_action(
	'init',
	function() {
		if ( ! function_exists( 'charitable' ) ) {
			return;
		}

		// Get the API.
		$fields_api = charitable()->donation_fields();

		// Shipping name field.
		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_name',
				[
					'label'          => 'Shipping Name',
					'data_type'      => 'meta',
					'donation_form'  => [
						'label'    => 'Name',
						'priority' => 1,
						'section'  => 'shipping-details',
						'type'     => 'text',
					],
					'admin_form'     => true,
					'show_in_meta'   => false,
					'show_in_export' => true,
					'email_tag'      => false,
				]
			)
		);

		// Shipping address field.
		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_address',
				[
					'label'          => 'Shipping Address',
					'data_type'      => 'meta',
					'donation_form'  => [
						'label'    => 'Address',
						'priority' => 2,
						'section'  => 'shipping-details',
						'type'     => 'text',
					],
					'admin_form'     => true,
					'show_in_meta'   => false,
					'show_in_export' => true,
					'email_tag'      => false,
				]
			)
		);

		// Shipping address line 2 field.
		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_address_2',
				[
					'label'          => 'Shipping Address 2',
					'data_type'      => 'meta',
					'donation_form'  => [
						'label'    => 'Address 2',
						'priority' => 3,
						'section'  => 'shipping-details',
						'type'     => 'text',
					],
					'admin_form'     => true,
					'show_in_meta'   => false,
					'show_in_export' => true,
					'email_tag'      => false,
				]
			)
		);

		// Shipping City field.
		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_city',
				[
					'label'          => 'Shipping City',
					'data_type'      => 'meta',
					'donation_form'  => [
						'label'    => 'City',
						'priority' => 4,
						'section'  => 'shipping-details',
						'type'     => 'text',
					],
					'admin_form'     => true,
					'show_in_meta'   => false,
					'show_in_export' => true,
					'email_tag'      => false,
				]
			)
		);

		// Shipping Postcode field.
		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_postcode',
				[
					'label'          => 'Shipping ZIP Code',
					'data_type'      => 'meta',
					'donation_form'  => [
						'label'    => 'ZIP Code',
						'priority' => 5,
						'section'  => 'shipping-details',
						'type'     => 'text',
					],
					'admin_form'     => true,
					'show_in_meta'   => false,
					'show_in_export' => true,
					'email_tag'      => false,
				]
			)
		);

		// Shipping State field.
		$states = [
			'outside-usa' => 'Outside USA',
			'US States'   => include( charitable()->get_path( 'directory', true ) . 'i18n/states/US.php' ),
		];

		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_state',
				[
					'label'          => 'Shipping State',
					'data_type'      => 'meta',
					'donation_form'  => [
						'label'    => 'State',
						'priority' => 5,
						'section'  => 'shipping-details',
						'type'     => 'select',
						'options'  => $states,
					],
					'admin_form'     => true,
					'show_in_meta'   => false,
					'show_in_export' => true,
					'email_tag'      => false,
				]
			)
		);

		// Add a Country field.
		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_country',
				[
					'label'          => 'Shipping Country',
					'data_type'      => 'meta',
					'donation_form'  => [
						'label'    => 'Country',
						'priority' => 5,
						'section'  => 'shipping-details',
						'type'     => 'select',
						'default'  => 'US',
						'options'  => charitable_get_helper( 'locations' )->get_countries(),
					],
					'admin_form'     => true,
					'show_in_meta'   => false,
					'show_in_export' => true,
					'email_tag'      => false,
				]
			)
		);

		// Add a Shipping Address Formatted field.
		$fields_api->register_field(
			new Charitable_Donation_Field(
				'shipping_address_formatted',
				[
					'label'          => 'Shipping Address',
					'data_type'      => 'meta',
					/**
					 * Get the formatted shipping address.
					 *
					 * @param  Charitable_Donation $donation Donation object.
					 * @return string
					 */
					'value_callback' => function( $donation ) {
						$address_fields = [
							'first_name' => $donation->get( 'shipping_name' ),
							'address'    => $donation->get( 'shipping_address' ),
							'address_2'  => $donation->get( 'shipping_address_2' ),
							'city'       => $donation->get( 'shipping_city' ),
							'postcode'   => $donation->get( 'shipping_postcode' ),
							'state'      => $donation->get( 'shipping_state' ),
							'country'    => $donation->get( 'shipping_country' ),
						];

						return charitable_get_helper( 'locations' )->get_formatted_address( $address_fields );
					},
					'donation_form'  => false,
					'admin_form'     => false,
					'show_in_meta'   => true,
					'show_in_export' => true,
					'email_tag'      => [
						'description' => 'Shipping address',
						'tag'         => 'shipping_address',
					],
				]
			)
		);
	}
);
