<?php
/*
Plugin Name: Calculate Price SQM
Description: A plugin to calculate the price of cutting grass per square meter.
Version: 1.0
Author: Your Name
*/

function calculate_price_sqm_enqueue_scripts() {
    wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' );
    wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), '', true );
}
add_action( 'wp_enqueue_scripts', 'calculate_price_sqm_enqueue_scripts' );

function calculate_grass_cutting_price( $sqm ) {
    $price_per_sqm = 0.5;
    $area = floatval( $sqm );
    if ( $area > 0 ) {
        return $area * $price_per_sqm;
    }
    return false;
}

function calculate_price_sqm_shortcode() {
    $image_url = plugins_url( 'assets/images/lawn-mower.jpg', __FILE__ );
    return '<img src="' . esc_url( $image_url ) . '" alt="Lawn mower" class="img-fluid mb-3" />';
}
add_shortcode( 'calculate_price_sqm', 'calculate_price_sqm_shortcode' );

add_action( 'gform_after_submission_5', 'calculate_price_after_submission', 10, 2 );
function calculate_price_after_submission( $entry, $form ) {
    // Find the 'Quantity' and 'Price' fields
    $quantity_field_id = '';
    $price_field_id = '';

    foreach ( $form['fields'] as $field ) {
        if ( $field->label == 'Quantity' ) {
            $quantity_field_id = $field->id;
        }
        if ( $field->label == 'Price' ) {
            $price_field_id = $field->id;
        }
    }

    if ( ! empty( $quantity_field_id ) && ! empty( $price_field_id ) ) {
        $quantity = rgar( $entry, $quantity_field_id );
        $price = calculate_grass_cutting_price( $quantity );

        if ( $price !== false ) {
            // Update the 'Price' field in the entry
            GFAPI::update_entry_field( $entry['id'], $price_field_id, $price );
        }
    }
}

add_filter( 'gform_confirmation_5', 'custom_confirmation_message', 10, 4 );
function custom_confirmation_message( $confirmation, $form, $entry, $ajax ) {
    // Find the 'Price' field
    $price_field_id = '';
    foreach ( $form['fields'] as $field ) {
        if ( $field->label == 'Price' ) {
            $price_field_id = $field->id;
        }
    }

    if ( ! empty( $price_field_id ) ) {
        $price = rgar( $entry, $price_field_id );
        $confirmation .= '<div class="alert alert-success mt-3">The calculated price is: $' . number_format( $price, 2 ) . '</div>';
    }

    return $confirmation;
}
