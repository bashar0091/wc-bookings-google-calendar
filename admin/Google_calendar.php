<?php

namespace WcBookingCalender;

session_start();

use Google\Client;
use Google\Service\Calendar;

class Google_calendar
{
    public function create_google_calendar()
    {
        add_action('init', array($this, 'create_calendar_handler'));
    }

    public function create_calendar_handler()
    { 
        $googleClientIdValue = esc_attr(get_option('google_client_id'));
        $googleClientSecretValue = esc_attr(get_option('google_client_secret'));
        $googleCalendarRedirectValue = esc_attr(get_option('google_calendar_redirect'));

        $client = new Client();
        $client->setClientId($googleClientIdValue);
            $client->setClientSecret($googleClientSecretValue);
            $client->setRedirectUri($googleCalendarRedirectValue);
            $client->addScope(\Google_Service_Calendar::CALENDAR);

            // // Authorize the client
            $client->setAccessType('offline');
            $client->setPrompt('consent');

        if( isset($_GET['CalendarGenerate']) ) {

            $_SESSION['calendarName'] = $_GET['calendarName'];
            $_SESSION['productId'] = $_GET['productId'];

            $authUrl = $client->createAuthUrl();
            header('Location: ' . $authUrl);
            exit;
        }

        if( isset($_GET['code']) ) {
            $googleCalToken = $_GET['code'];

            $client->fetchAccessTokenWithAuthCode($googleCalToken);

            $service = new Calendar($client);

            $calendar = new Calendar\Calendar();
            $calendar->setSummary($_SESSION['calendarName']);
            $calendar->setTimeZone('America/Los_Angeles');

            $createdCalendar = $service->calendars->insert($calendar);

            global $wpdb;
            $table_name = $wpdb->prefix . 'google_calendar_id';
            $productID = $_SESSION['productId'];
            $calendarId = $createdCalendar->getId();

            $data = array(
                'calendar_id' => $calendarId,
            );

            $where = array(
                'product_id' => $productID,
            );

            $wpdb->update(
                $table_name,
                $data,
                $where
            );

            wp_redirect( home_url() . '/wp-admin');
            exit;
        }
    }

}
