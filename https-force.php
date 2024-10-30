<?php
/*
 * Plugin Name: HTTPS force
 * Plugin URI: https://en.wordpress.org/plugins/https-force
 * Description: This Plugin forces HTTPS protocol on internal links and references and will redirect to a secure version of the site.
 * Version: 1.1.0
 * Author: Thomas Vang (sitzz)
 * Author URI: http://sitzz.dk/
 * License: GPLv3
 */

if (!defined('ABSPATH')) {
    exit;
}

class SitzzForceHttps
{
   /*
    * __construct: Constructor function
    * Checks if site uses SSL.
    * If it does; Ensures functions to replace HTTP with HTTPS for all internal links
    * If it does NOT; Redirect to HTTPS site if enabled
    */
    public function __construct()
    {
        add_action(
            'admin_init',
            array(
                $this,
                'theForceSettings'
            )
        );

        if (!is_ssl() && get_option('SitzzForceHttps_redirect_to_secure_site-id')) {
            add_action(
                'theForceRedirect',
                array(
                    $this,
                    'theForceRedirect'
                ),
                1
            );
        } else {
            add_action(
                'wp_loaded',
                array(
                    $this,
                    'theForceLoad'
                ),
                99,
                1
            );
        }
    }
  
   /*
    * theForceRedirect: Redirect function
    * Registers redirect function for non-secure sites
    */
    public function theForceRedirect()
    {
        wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
        exit();
    }

   /*
    * theForceLoad: Loader function
    * Registers the 'theForceMagic' function
    */
    public function theForceLoad()
    {
        ob_start(
            array(
                $this,
                'theForceMagic'
            )
        );
    }

   /*
    * theForceSettings: Add plugin settings to WordPress general settings page
    *
    */
    public function theForceSettings()
    {
        add_settings_field('SitzzForceHttps_replace_insecure_elements-id', 'Replace insecure elements', array($this, 'theForceSettingsReplace'), 'general');
        add_settings_field('SitzzForceHttps_redirect_to_secure_site-id', 'Redirect to secure site', array($this, 'theForceSettingsRedirect'), 'general');
        register_setting('general', 'SitzzForceHttps_replace_insecure_elements-id', array('type'=>'boolean', 'default'=>false));
        register_setting('general', 'SitzzForceHttps_redirect_to_secure_site-id', array('type'=>'boolean', 'default'=>false));
    }

    /*
    * theForceSettingsReplace: Returns the form element for the elemet replace setting
    *
    */
    public function theForceSettingsReplace()
    {
        $replChecked = get_option('SitzzForceHttps_replace_insecure_elements-id') ? ' checked="checked"' : '';
        echo '<input type="checkbox" name="SitzzForceHttps_replace_insecure_elements-id" id="SitzzForceHttps_replace_insecure_elements-id-id" value="true"' . $replChecked . ' />';
    }

    /*
    * theForceSettingsForm: Returns the form element for the redirect setting
    *
    */
    public function theForceSettingsRedirect()
    {
        $redirChecked = get_option('SitzzForceHttps_redirect_to_secure_site-id') ? ' checked="checked"' : '';
        echo '<input type="checkbox" name="SitzzForceHttps_redirect_to_secure_site-id" id="SitzzForceHttps_redirect_to_secure_site-id" value="true"' . $redirChecked . ' />';
    }

   /*
    * theForceMagic: Magic function
    * Replaces HTTP in links and references to internal resources with HTTPS
    */
    public function theForceMagic($buffer)
    {
        if (!get_option('SitzzForceHttps_replace_insecure_elements-id')) {
            return $buffer;
        }

        $content_type = null;
        foreach (headers_list() as $header) {
            if (strpos(strtolower($header), 'content-type:') === 0) {
                $pieces = explode(':', strtolower($header));
                $content_type = trim($pieces[1]);
                break;
            }
        }

        if (is_null($content_type) || substr($content_type, 0, 9) === 'text/html') {
            // Take care of 'href' links
            $buffer = str_replace('href=\'http://' . $_SERVER['HTTP_HOST'], 'href=\'https://' . $_SERVER['HTTP_HOST'], $buffer);
            $buffer = str_replace('href="http://' . $_SERVER['HTTP_HOST'], 'href="https://' . $_SERVER['HTTP_HOST'], $buffer);

            // Take care of 'src' references
            $buffer = str_replace('src=\'http://' . $_SERVER['HTTP_HOST'], 'src=\'https://' . $_SERVER['HTTP_HOST'], $buffer);
            $buffer = str_replace('src="http://' . $_SERVER['HTTP_HOST'], 'src="https://' . $_SERVER['HTTP_HOST'], $buffer);

            // Take care or 'content' references
            $buffer = str_replace('content=\'http://' . $_SERVER['HTTP_HOST'], 'content=\'https://' . $_SERVER['HTTP_HOST'], $buffer);
            $buffer = str_replace('content="http://' . $_SERVER['HTTP_HOST'], 'content="https://' . $_SERVER['HTTP_HOST'], $buffer);

            // Take care of 'url' links
            $buffer = str_replace('url(\'http://' . $_SERVER['HTTP_HOST'], 'url(\'https://' . $_SERVER['HTTP_HOST'], $buffer);
            $buffer = str_replace('url("http://' . $_SERVER['HTTP_HOST'], 'url("https://' . $_SERVER['HTTP_HOST'], $buffer);

            // Take care of 'loaderUrl' references
            $buffer = str_replace('http:\/\/' . $_SERVER['HTTP_HOST'], 'https:\/\/' . $_SERVER['HTTP_HOST'], $buffer);

            // Take care of URLs in text - but attempt to ignore form data in admin pages
            if (is_admin()) {
                $buffer = str_replace('value="http://' . $_SERVER['HTTP_HOST'], 'value="ptth://' . $_SERVER['HTTP_HOST'], $buffer);
	    }
            $buffer = str_replace('http://' . $_SERVER['HTTP_HOST'], 'https://' . $_SERVER['HTTP_HOST'], $buffer);

            // Take care of Google URLs
            $buffer = str_replace('http://fonts.googleapis.com', 'https://fonts.googleapis.com', $buffer);
            $buffer = str_replace('http://maps.googleapis.com', 'https://maps.googleapis.com', $buffer);
            $buffer = str_replace('http://ajax.googleapis.com', 'https://ajax.googleapis.com', $buffer);
            $buffer = str_replace('http://storage.googleapis.com', 'https://storage.googleapis.com', $buffer);

            // Fix for visible links
	    $buffer = str_replace('>http://' . $_SERVER['HTTP_HOST'], '>https://' . $_SERVER['HTTP_HOST'], $buffer);

            // Remove any tmp:// left in there...
            $buffer = str_replace('ptth://', 'http://', $buffer);
        }
    
        // Return the new contents...
        return $buffer;
    }

    public function setSecureHooks($url)
    {
        // Replace HTTP with HTTPS if enabled
        if (get_option('SitzzForceHttps_replace_insecure_elements-id')) {
            return str_replace('http://', 'https://', $url);
        }

    }
}

/*
 * Load class...
 */
new SitzzForceHttps();

add_filter('script_loader_src', array('SitzzForceHttps', 'setSecureHooks'));
add_filter('style_loader_src', array('SitzzForceHttps', 'setSecureHooks'));

