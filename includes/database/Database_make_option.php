<?php

namespace WcBCDatabase;

class Database_make_option
{
    public function insert_option()
    {
        add_action('admin_init', array($this, 'create_option'));
    }

    public function create_option()
    {
        $googleClientId = get_option('google_client_id');
        $googleClientSecret = get_option('google_client_secret');
        $googleCalendarRedirect = get_option('google_calendar_redirect');
        $googleCalendarToken = get_option('google_calendar_token');
        $googleCalendarTimezone = get_option('google_calendar_Timezone');

        if (empty($googleClientId)) {
            update_option('google_client_id', null);
        }

        if (empty($googleClientSecret)) {
            update_option('google_client_secret', null);
        }

        if (empty($googleCalendarRedirect)) {
            update_option('google_calendar_redirect', null);
        }

        if (empty($googleCalendarTimezone)) {
            update_option('google_calendar_Timezone', null);
        }
    }
}