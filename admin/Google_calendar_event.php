<?php

namespace WcBookingCalender;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class Google_calendar_event {

    public function create_google_calendar_event() {
        add_action('woocommerce_thankyou', array($this, 'custom_order_placed_action'));
    }

    public function custom_order_placed_action($order_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'google_calendar_id';

        $order = wc_get_order($order_id);

        $is_booking_product = false;
        $items = $order->get_items();

        foreach ($items as $item) {
            $product_type = $item->get_product()->get_type();
            $product_id = $item->get_product_id();
            $product_name = $item->get_name();

            if ($product_type === 'booking') {
                $is_booking_product = true;

                break;
            }
        }

        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $fullName = $first_name . ' ' . $last_name;

        // get the booking id 
        $table_name_post = $wpdb->prefix . 'posts';
        $results = $wpdb->get_col("SELECT id FROM $table_name_post WHERE post_parent = $order_id ");
        
        $book_id = $results[0];

        $booking_start = get_post_meta($book_id, '_booking_start', true);
        $booking_end = get_post_meta($book_id, '_booking_end', true);

        $booking_start_date = \DateTime::createFromFormat('YmdHis', $booking_start);
        $booking_end_date = \DateTime::createFromFormat('YmdHis', $booking_end);

        $start_formatted_date = $booking_start_date->format('Y-m-d\TH:i:s');
        $end_formatted_date = $booking_end_date->format('Y-m-d\TH:i:s');

        if ($is_booking_product) {
            $product_id_column = 'product_id';
            $calendar_id_column = 'calendar_id';

            $query = $wpdb->prepare(
                "SELECT {$calendar_id_column} FROM {$table_name} WHERE {$product_id_column} = %d",
                $product_id
            );

            // Retrieve the calendar_id from the table
            $calendar_id = $wpdb->get_var($query);

            if ($calendar_id) {

                $credentials = __DIR__ . '/credentials.json';

                $client = new Google_Client();
                $client->setApplicationName('testoo');
                $client->setScopes(array(Google_Service_Calendar::CALENDAR));
                $client->setAuthConfig($credentials);
                $client->setAccessType('offline');
                $client->getAccessToken();
                $client->getRefreshToken();

                $googleCalendarTimezone = esc_attr(get_option('google_calendar_Timezone'));

                $service = new Google_Service_Calendar($client);

                $event = new Google_Service_Calendar_Event(array(
                    'summary' => 'New Event with ' . $fullName,
                    'description' => 'Event : ' . $product_name,
                    'start' => array(
                        'dateTime' => $start_formatted_date,
                        'timeZone' => $googleCalendarTimezone,
                    ),
                    'end' => array(
                        'dateTime' => $end_formatted_date,
                        'timeZone' => $googleCalendarTimezone,
                    ),
                ));

                $GooglecalendarId = $calendar_id;
                $event = $service->events->insert($GooglecalendarId, $event);
            }
        }
    }
}
