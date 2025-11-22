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

function calculate_price_sqm_shortcode() {
    $price_per_sqm = 0.5;
    $total_price = '';

    if ( isset( $_POST['calculate_price'] ) && isset( $_POST['sqm'] ) ) {
        $sqm = floatval( $_POST['sqm'] );
        if ( $sqm > 0 ) {
            $calculated_price = $sqm * $price_per_sqm;
            $total_price = '<div class="alert alert-success mt-3">The calculated price is: $' . number_format( $calculated_price, 2 ) . '</div>';
        } else {
            $total_price = '<div class="alert alert-danger mt-3">Please enter a valid area.</div>';
        }
    }

    $image_url = plugins_url( 'assets/images/lawn-mower.jpg', __FILE__ );

    ob_start();
    ?>
    <div class="container">
        <img src="<?php echo esc_url( $image_url ); ?>" alt="Lawn mower" class="img-fluid mb-3" />
        <form method="post">
            <div class="mb-3">
                <label for="sqm" class="form-label">Area in Square Meters</label>
                <input type="number" step="0.01" class="form-control" id="sqm" name="sqm" required>
            </div>
            <button type="submit" name="calculate_price" class="btn btn-primary">Calculate Price</button>
        </form>
        <?php echo $total_price; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'calculate_price_sqm', 'calculate_price_sqm_shortcode' );
