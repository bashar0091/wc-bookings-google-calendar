<?php

namespace WcBCDatabase;

class Database_table
{
    private $wpdb;
    
    public function create_table()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $table_name = $this->wpdb->prefix . 'google_calendar_id';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT NOT NULL AUTO_INCREMENT,
            product_id BIGINT(20) UNSIGNED NOT NULL,
            calendar_id VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (product_id) REFERENCES {$wpdb->prefix}posts(ID)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Check if the table already exists
        $existing_table = $this->wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        
        if ($existing_table !== $table_name) {
            dbDelta($sql);
        }
    }

    public function insert_product_ids()
    {
        $product_ids = get_posts(array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ));

        if (empty($product_ids)) {
            return; // No product IDs found
        }

        $table_name = $this->wpdb->prefix . 'google_calendar_id';

        foreach ($product_ids as $product_id) {
            $existing_row = $this->wpdb->get_row($this->wpdb->prepare(
                "SELECT * FROM $table_name WHERE product_id = %d",
                $product_id
            ));

            if (!$existing_row) {
                $this->wpdb->insert($table_name, array('product_id' => $product_id));
            }
        }
    }

}
