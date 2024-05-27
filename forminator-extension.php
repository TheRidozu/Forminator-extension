<?php

/*
 * Plugin Name: Forminator extension
 * Version: 1.0.0
 * Author: Hubert Krzyszczyk
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

function forminator_not_active_notice() {
    echo '<div class="error"><p>' . __( 'Forminator Validation & Limits plugin requires the Forminator plugin to be active.', 'forminator-validation-limits' ) . '</p></div>';
}

function check_forminator_plugin_state() {
    if ( ! class_exists( 'Forminator_API' ) ) {
        add_action( 'admin_notices', 'forminator_not_active_notice' );
        return;
    }
}

add_action('admin_init', 'check_forminator_plugin_state');

function form_email_validation( $submit_errors, $form_id, $field_data_array ) {
    if ( isset($_POST['email-1']) && isset($_POST['email-2']) ) {

        $fields = [];
        $fields[] = $_POST['email-1'];
        $fields[] = $_POST['email-2'];

        if ( count(array_unique($fields)) !== 1 ) {
            $submit_errors[][ 'email-2'] = __( 'E-maile nie są takie same' );
        }

        return $submit_errors;
    }
}

add_filter('forminator_custom_form_submit_errors', 'form_email_validation', 10, 3);

function forminator_form_ext_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id' => null,
        'limit' => 30
    ), $atts, 'forminator_form_ext');

    $form_id = $atts['id'];
    $limit = $atts['limit'];

    if ( is_wp_error( Forminator_API::get_form( $form_id ) ) ) {
        return "Form with ID $form_id does not exist";
    }

    if ( Forminator_API::count_entries( $form_id ) >= $limit ) {
        return "Brak miejsc na szkolenie";
    }

    return do_shortcode("[forminator_form id=$form_id]");
}

function register_forminator_form_ext_shortcode() {
    add_shortcode( 'forminator_form_ext', 'forminator_form_ext_shortcode' );
}

add_action( 'init', 'register_forminator_form_ext_shortcode' );