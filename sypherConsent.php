<?php
/*
Plugin Name: Sypher Cookie Consent
Description: Choose the Sypher Cookie Consent Manager to improve your data privacy practices and help your organization with the ePrivacy Directive and GDPR. The EU legislation requires consent for the placement of cookies and a lawful basis for certain processing of the data from cookies stored on users devices (computers, laptops, mobile phones or tablets). This module offers a number of features designed to make the compliance process easier for the display of cookie banners, appropriate placement of cookies and capturing consent.
Version: 2023.1.0
Author: Sypher
Author URI: https://www.sypher.eu/
License: GPLv3 or later
Text Domain: sypher
*/
add_action( 'admin_menu', 'sypherCcm_add_admin_menu' );
add_action( 'admin_init', 'sypherCcm_settings_init' );


function sypherCcm_add_admin_menu()
{
    add_options_page( 'Sypher Cookie Consent', 'Sypher Cookie Consent', 'manage_options', 'sypher_consent', 'sypherCcm_options_page' );
}


function sypherCcm_settings_init()
{
    register_setting( 'pluginPage', 'sypherCcm_settings' );
    add_settings_section(
        'sypherCcm_pluginPage_section',
        __( 'In order to get your website UUID, an account is required in Sypher. <br><br><a href="https://www.sypher.eu/cookie-consent-management" class="button">Get started FREE</a>', 'sypher.eu' ),
        'sypherCcm_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'websiteUuid',
        __( 'Website UUID:', 'sypher.eu' ),
        'sypherCcm_website_uuid_render',
        'pluginPage',
        'sypherCcm_pluginPage_section'
    );
}


function sypherCcm_website_uuid_render()
{

    $options = get_option( 'sypherCcm_settings' );
    ?>
    <input type='text' name='sypherCcm_settings[websiteUuid]' value='<?php echo esc_html($options['websiteUuid']); ?>'>
    <?php

}


function sypherCcm_settings_section_callback(  )
{
    ?>
    <hr>
    <br>
    <?php
}


function sypherCcm_options_page()
{
    ?>
    <form action='options.php' method='post'>

        <h2>Sypher Cookie Consent - Configuration</h2>

        <a href='https://www.sypher.eu' target="_blank">
            <?php printf(
                '<img src="%1$s" alt="Sypher" />',
                plugins_url( '/assets/images/sypher-blue.png', __FILE__ )
            ); ?>
        </a>
        <p>Add your website UUID from Sypher. This will make the cookie banner show up on your website and will automatically detect the website language and configure the language of the Sypher windows accordingly. It will default to English if the language cannot be detected.  To configure the look and feel of the cookie banner, access the <a href="https://app.sypher.eu/ccm">Cookie Consent Manager</a> section in Sypher.</p>
        <li><a href="https://app.sypher.eu/help/ccm/integration_code">Detailed usage instructions</a></li>
        <li><a href="https://app.sypher.eu/help/ccm/cookietable">Displaying the list of cookies</a></li>

        <?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
        ?>

    </form>
    <?php

}
add_action('wp_enqueue_scripts', 'sypherCcm_add_script_wp_head');
function sypherCcm_add_script_wp_head()
{
    $sypherCcmScriptsAppPath = 'https://cdn.sypher.eu/consent/';
    $sypherCcmScriptsConfigPath = 'https://consent.sypher.eu/cnst/';

    $options = get_option('sypherCcm_settings');
    if (isset($options['websiteUuid']) && !empty($options['websiteUuid'])) {
        $websiteUuid = $options['websiteUuid'];
        $websiteConfigCss = $sypherCcmScriptsConfigPath . esc_html($websiteUuid) . '.css';
        $websiteConfigJs = $sypherCcmScriptsConfigPath . esc_html($websiteUuid) . '.js';

        wp_enqueue_script( 'sypherCcm',$sypherCcmScriptsAppPath . 'script.min.js');
        wp_enqueue_script( 'sypherCcm-config',$websiteConfigJs, array('sypherCcm'));

        wp_enqueue_style( 'sypherCcm',$sypherCcmScriptsAppPath . 'style.min.css');
        wp_enqueue_style( 'sypherCcm-config',$websiteConfigCss, array('sypherCcm'));
    }
}

add_action( 'wp_enqueue_scripts', 'sypherCcm_add_script_wp_footer' );
function sypherCcm_add_script_wp_footer()
{
    $options = get_option('sypherCcm_settings');
    if (isset($options['websiteUuid']) && !empty($options['websiteUuid'])) {
        wp_register_script( 'sypherCcm-run', '', [], '', true );
        wp_enqueue_script( 'sypherCcm-run'  );
        wp_add_inline_script( 'sypherCcm-run', 'startCookieConsent()' );
    }
}
