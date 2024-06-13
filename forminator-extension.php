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

            return $submit_errors;
        }

        
        if ( isEmailInDb($form_id, $_POST['email-1']) ) {
            $submit_errors[]['email-2'] = __("Podany e-mail jest już zapisany");

            return $submit_errors;
        }

        $limit = isset($_POST['forminator_form_limit']) ? intval($_POST['forminator_form_limit']) : null;
        
        if ( $limit && Forminator_API::count_entries( $form_id ) >= $limit ) {
            $submit_errors[]['email-2'] = __('Przekroczono limit zgłoszeń dla tego formularza' );
        }

        return $submit_errors;
    }
}

add_filter('forminator_custom_form_submit_errors', 'form_email_validation', 10, 3);

function isEmailInDb( $form_id, $email ) {
    $db_emails = array();
    
    foreach( Forminator_API::get_entries( $form_id, 'email' ) as $entry ) {
        array_push($db_emails, $entry->meta_data['email-1']['value']);
    }
    
    return in_array($email, $db_emails);
}

function forminator_form_ext_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id' => null,
        'limit' => 30
    ), $atts, 'forminator_form_ext');

    $form_id = $atts['id'];
    $limit = $atts['limit'];

    global $post;
    
    if ( has_shortcode( $post->post_content, 'forminator_form_ext' ) ) {
        wp_enqueue_script( 'forminator-extension-script', plugins_url( '/assets/js/forminator-extension.js', __FILE__ ), array(), '1.0.0', true );
    }

    if ( is_wp_error( Forminator_API::get_form( $form_id ) ) ) {
        return "Form with ID $form_id does not exist";
    }

    add_filter('forminator_render_form_submit_markup', function( $html, $form_ID ) use ( $form_id, $limit ) {
        if ( (int) $form_ID === (int) $form_id ) {
            $html .= '<input type="hidden" name="forminator_form_limit" value="' . esc_attr($limit) . '" />';
        }
        
        return $html;
    }, 10, 2);

    $form_html = do_shortcode("[forminator_form id=$form_id]");

    if ( Forminator_API::count_entries( $form_id ) >= $limit ) {
        return "<p>Brak miejsc na szkolenie</p>";
    }

    return $form_html;
}

function register_forminator_form_ext_shortcode() {
    add_shortcode( 'forminator_form_ext', 'forminator_form_ext_shortcode' );
}

add_action( 'init', 'register_forminator_form_ext_shortcode' );
