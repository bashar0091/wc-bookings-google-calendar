<?php

namespace WcBookingCalender;

class Admin_menu
{
    public function register_custom_menu()
    {
        add_action('admin_menu', array($this, 'add_custom_menu'));
    }

    public function add_custom_menu()
    {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        add_submenu_page(
            'edit.php?post_type=wc_booking',
            'My Custom Submenu', 
            'Google Calendar',
            'manage_options', 
            'google_calendar', 
            array($this, 'display_custom_submenu_page')
        );
    }

    public function display_custom_submenu_page()
    {
        // Retrieve option values
        $googleClientIdValue = esc_attr(get_option('google_client_id'));
        $googleClientSecretValue = esc_attr(get_option('google_client_secret'));
        $googleCalendarRedirectValue = esc_attr(get_option('google_calendar_redirect'));
        $googleCalendarTimezone = esc_attr(get_option('google_calendar_Timezone'));
        $googleJson = esc_attr(get_option('google_json'));

        if (isset($_GET['success_message'])) {
            $success_message = sanitize_text_field($_GET['success_message']);
            echo '<div class="notice notice-success is-dismissible"><p>' . $success_message . '</p></div>';
        }    
        
        // upload json file 
        if (isset($_POST['JsonSubmit'])) {
            $file = $_FILES['json_file'];
            $upload_dir = dirname(plugin_dir_path(__FILE__)) . '/admin/uploads/';
    
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
    
            $file_path = $upload_dir . basename('credentials.json');
    
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                update_option('google_json', basename('credentials.json'));

                wp_redirect($_SERVER['REQUEST_URI']);
                exit;
            }
        }

        // delete file 
        if (isset($_POST['JsonDelete'])) {
            $file_path = dirname(plugin_dir_path(__FILE__)) . '/admin/uploads/credentials.json';
        
            if (unlink($file_path)) {
                update_option('google_json', null);

                wp_redirect($_SERVER['REQUEST_URI']);
                exit;
            } 
        }
        
        echo '
        <div class="wrap">
            <h1 class="wp-heading-inline">Google API Configuration</h1>
            
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="google-client-id">Google Client ID:</label></th>
                        <td><input type="text" id="google-client-id" name="google-client-id" value="' . $googleClientIdValue . '" required></td>
                    </tr>
                    <tr>
                        <th><label for="google-client-secret">Google Client Secret:</label></th>
                        <td><input type="text" id="google-client-secret" name="google-client-secret" value="' . $googleClientSecretValue . '" required></td>
                    </tr>
                    <tr>
                        <th><label for="google-calendar-redirect">Google Calendar Redirect:</label></th>
                        <td><input type="text" id="google-calendar-redirect" name="google-calendar-redirect" value="' . $googleCalendarRedirectValue . '" required></td>
                    </tr>
                    <tr>
                        <th><label for="google-calendar-timezone">Timezone:</label></th>
                        <td><input type="text" id="google-calendar-timezone" name="google-calendar-timezone" value="' . $googleCalendarTimezone . '" required></td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="googleAPiSubmit" class="button button-primary" value="Update">
                </p>
            </form>
        </div>
        <hr>
        <hr> ';

        echo '
        <div class="wrap">
            <h1 class="wp-heading-inline">Upload Json File</h1>';

            if ($googleJson == null) {
                echo '
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="json_file" required>
                    <input type="submit" class="button button-secondary" name="JsonSubmit" value="Upload">
                </form>';
            } else {
                echo '
                <form method="POST">
                    <label>'. $googleJson .' file is added, click here to delete</label>
                    <input type="submit" class="button button-danger" name="JsonDelete" value="Delete">
                </form>';
            }

        echo '
        </div>';

    }

}
