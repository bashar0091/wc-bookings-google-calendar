<?php

namespace WcBookingCalender;

class Product_table_option
{
    public function table_column()
    {
        add_filter( 'manage_edit-product_columns', array($this, 'custom_wc_product_table_columns') );
        add_action( 'manage_product_posts_custom_column', array($this, 'custom_wc_product_table_column_content'), 10, 2 );
    }

    public function custom_wc_product_table_columns( $columns ) {
        $columns['generate_calendar'] = 'Calendar';

        return $columns;
    }
    
    public function custom_wc_product_table_column_content( $column_name, $post_id ) {
        global $post, $wpdb;

        $product = wc_get_product($post_id);
        
        $product_slug = $product->get_slug();
        $product_id = $product->get_id();
        $product_type = $product->get_type();

        if ($column_name == 'generate_calendar' && $post->post_status === 'publish') {

            $table_name = $wpdb->prefix . 'google_calendar_id';
            $calendar_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT calendar_id FROM $table_name WHERE product_id = %d",
                    $product_id
                )
            );
            if ($calendar_id) {
                echo 'Generated';
            } else {
                if($product_type == 'booking') {
                    echo '<form action="" method="get">';
                    echo '<input type="hidden" name="calendarName" value="' . $product_slug . '">';
                    echo '<input type="hidden" name="productId" value="' . $product_id . '">';
                    echo '<button type="submit" name="CalendarGenerate" class="button button-secondary"> Generate </button>';
                    echo '</form>';
                } else {
                    echo 'Not Booking Product';
                }
            }
        }
    }
}