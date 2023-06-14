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
        global $post;

        $product = wc_get_product($post_id);
        $product_slug = $product->get_slug();
        $product_id = $product->get_id();

        if ($column_name == 'generate_calendar' && $post->post_status === 'publish') {
            echo '<form action="" method="get">';
                echo '<input type="hidden" name="calendarName" value="' . $product_slug . '">';
                echo '<input type="hidden" name="productId" value="' . $product_id . '">';
                echo '<button type="submit" name="CalendarGenerate" class="button button-secondary"> Generate </button>';
            echo '</form>';
        }
    }
}