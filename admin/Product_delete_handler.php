<?php

namespace WcBookingCalender;

class Product_delete_handler 
{
    public function product_delete() {
        add_action('before_delete_post', array($this, 'product_delete_handler'));
    }

    public function product_delete_handler($post_id) {
        if (get_post_type($post_id) === 'product') {
            global $wpdb;

            $table_name = $wpdb->prefix . 'google_calendar_id';
            $product_id = $post_id;

            $wpdb->delete(
                $table_name,
                array('product_id' => $product_id),
                array('%d')
            );
        }
    }
}