<?php

/**
 * Plugin Name: WC bookings google calendar
 * Plugin URI: pilar.fi
 * Description: This plugin expand the wc booking plugin functionality. If creates new calendar for each bookable product and save the booking events
 * Version: 1.0.0
 * Author: Pilar Dev
 * Author URI: pilar.fi
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use WcBookingCalender\Admin_menu;
use WcBookingCalender\Google_calendar;
use WcBookingCalender\Google_calendar_event;
use WcBookingCalender\Product_table_option;
use WcBookingCalender\Product_delete_handler;

use WcBCDatabase\Database_table;
use WcBCDatabase\Database_make_option;
use WcBCDatabase\Calendar_credentials_handler;

/**
 * Initialize the plugin.
 */
function wc_booking_google_calendar_init() {
    $wc_booking_google_calendar = new Wc_booking_google_calendar();
    $wc_booking_google_calendar->init();
}
add_action('plugins_loaded', 'wc_booking_google_calendar_init');

class Wc_booking_google_calendar
{
    /**
     * Plugin version.
     *
     * @var string
     */
    private $version;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->version = '1.0.0';
        $this->load_dependencies();
    }

    /**
     * Load required dependencies for the plugin.
     */
    private function load_dependencies()
    {
        // Load any required files or libraries here.
    }

    /**
     * Initialize the plugin.
     */
    public function init()
    {
        $admin_menu = new Admin_menu();
        $admin_menu->register_custom_menu();

        $database_table = new Database_table();
        $database_table->create_table();
        $database_table->insert_product_ids();

        $database_make_option = new Database_make_option();
        $database_make_option->insert_option();

        $calendar_credentials_handler = new Calendar_credentials_handler();
        $calendar_credentials_handler->google_api_update();

        $google_calendar = new Google_calendar();
        $google_calendar->create_google_calendar(); 

        $google_calendar_event = new Google_calendar_event();
        $google_calendar_event->create_google_calendar_event(); 

        $product_table_option = new Product_table_option();
        $product_table_option->table_column();

        $product_delete_handler = new Product_delete_handler();
        $product_delete_handler->product_delete();
    }

    /**
     * Get the plugin version.
     *
     * @return string Plugin version.
     */
    public function get_version()
    {
        return $this->version;
    }
}