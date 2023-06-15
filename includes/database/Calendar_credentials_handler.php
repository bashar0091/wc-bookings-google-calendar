<?php

namespace WcBCDatabase;

class Calendar_credentials_handler
{
    public function google_api_update() {
        if (isset($_POST['googleAPiSubmit'])) {
            // Handle form submission
            $googleClientId = sanitize_text_field($_POST['google-client-id']);
            $googleClientSecret = sanitize_text_field($_POST['google-client-secret']);
            $googleCalendarRedirect = sanitize_text_field($_POST['google-calendar-redirect']);
            $googleCalendarTimezone = sanitize_text_field($_POST['google-calendar-timezone']);
            
            // Update options in the database
            update_option('google_client_id', $googleClientId);
            update_option('google_client_secret', $googleClientSecret);
            update_option('google_calendar_redirect', $googleCalendarRedirect);
            update_option('google_calendar_Timezone', $googleCalendarTimezone);

            // Set success message
            $success_message = "Updated successfully!";
            
            // Redirect to the custom submenu page with the success message
            wp_redirect(admin_url('edit.php?post_type=wc_booking&page=google_calendar&success_message=' . urlencode($success_message)));
            exit();
        }
    }
}