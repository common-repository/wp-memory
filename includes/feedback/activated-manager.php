<?php namespace wpmemoryPlugin_activate {
if (!defined("ABSPATH")) {
    die('We\'re sorry, but you can not directly access this file.');
}


    $bill_debug = false;
    //$bill_debug = true;


    if (is_multisite()) {
        return;
    }

    if (__NAMESPACE__ == "wpmemoryPlugin_activate") {
        $BILLPRODUCT = "wp-memory";
        $BILLPRODUCTNAME = "WP Memory Plugin";
        $BILLPRODUCTSLANGUAGE = "wp-memory";
        $BILLCLASS = "ACTIVATED_" . $BILLPRODUCT;
        $BILL_OPTIN = strtolower($BILLPRODUCT) . "_optin";
        $PRODUCT_URL = WPMEMORYURL;
        $PRODUCTVERSION = WPMEMORYVERSION;
    }


    $showroom_url = admin_url("tools.php?page=wp_memory_admin_page");
    $showroom_url .= "&tab=tools";





   // return;

   // debug2($BILLCLASS);
   $BILLCLASS_UPPER = strtoupper($BILLCLASS);


       if (!isset($_COOKIE[$BILLCLASS_UPPER]) or $bill_debug) { 

           echo '<div id="bill-activate-modal-wpmemory" class="bill-activate-modal-wpmemory" style="display:block" >';
           ?>

            <div class="bill-vote-message-wpmemory">
                        

                <?php if (wpmemory_errors_today(2)) {

                            echo '<h3>';
                            echo esc_attr($BILLPRODUCTNAME).' - ';

                            echo esc_attr__("PRE-INSTALLATION CHECKUP", "wpmemory");
                            echo '</h3>'; 

                            echo '<p style="color: red;">';
                            echo "Errors or warnings have been found in your server's error log for the last 48 hours. We recommend examining these errors and addressing them immediately to avoid potential issues, ensuring greater stability for your site.";
                            echo "<br />";
                            echo "Please note: We are only displaying errors that already exist and are recorded in your server's error log.";

                            echo "</p>";
                            ?>

                            <a href="https://wptoolsplugin.com/site-language-error-can-crash-your-site/" target="_blank">
                                <?php echo esc_attr__(
                                    "Learn More",
                                    "plugin_text_domain"
                                ); ?>
                            </a>
                            </p>
                            <br>
                            <?php
                            $all_plugins = get_plugins();
                            $is_wp_tools_installed = false;

                            foreach ($all_plugins as $plugin_info) {
                                if ($plugin_info["Name"] === "wptools") {
                                    $is_wp_tools_installed = true;
                                    break; // Exit the loop once found
                                }
                            }

                            if (!$is_wp_tools_installed) { ?>
                                If you'd like help with errors management, this free plugin can help.
                                <br>
                                <a href="#" id="bill-install-wptools" class="button button-primary bill-install-wpt-plugin-now">Install WPtools Free</a>
                                <img alt="aux" src="/wp-admin/images/wpspin_light-2x.gif" id="bill_imagewait" style="display:none" /> 
                                <button id="loading-spinner" class="button button-primary" style="display: none;" aria-label="Loading...">
                                    <span class="loading-text">Loading...</span>
                                </button>
                            <?php } ?>

                            <br /><br />

                            By proceeding, you agree that you have read and understood the 
                            <a href="https://siterightaway.net/terms-of-use-of-our-plugins-and-themes/" target="_blank">terms of use</a>
                            of our plugins and themes.
                            <br />
                            If you find any issues, please consider requesting free support before leaving feedback. 


               <?php } else {

                        // NO errorss found

                            echo '<h3>';
                            echo esc_attr__("Welcome!", "wpmemory");
                            echo '</h3>'; 

                            ?>
                            <br />
                            We have been developing WordPress plugins and themes for 10 years and now have a suite of 20 plugins and 6 themes serving thousands of users.
                            <br />
                            If you find any issues, please consider requesting free support before leaving feedback.
                            <br /> <br />

                            By proceeding, you agree that you have read and understood the 
                            <a href="https://siterightaway.net/terms-of-use-of-our-plugins-and-themes/" target="_blank">terms of use</a>
                            of our plugins and themes.

                    <?php
                    }
                    // end not errors
                    ?>

                    <form>                         
                                                
                        <br />  <br />

                        <a href="#" class="button button-primary" id="wpmemory-activate-close-up-dialog">
                        <?php esc_attr_e("CONTINUE", "wpmemory"); ?></a>

                        <br />  <br />


<input type="hidden" id="nonce" name="nonce" value="<?php echo esc_attr(wp_create_nonce("bill_install")); ?>" />
<input type="hidden" id="slug" name="slug" value="<?php echo esc_attr($BILLPRODUCT); ?>" />
                        <input type="hidden" id="showroom" name="showroom" value="<?php echo esc_attr(
                            $showroom_url
                        ); ?>" />
                        <br />
                    </form>

            </div>


    
            <?php
            add_option("wpmemory_activated_notice", "0");
            update_option("wpmemory_activated_notice", "0");

            $wtime = time() + 3600 * 24;
            $jsCode =
                "document.cookie = '" .
                $BILLCLASS .
                "=" .
                time() .
                "; expires=" .
                date("D, d M Y H:i:s", $wtime) .
                " UTC; path=/';";
           // echo '<script>' . esc_js($jsCode) . '</script>';
            



        } //nao tem cookie...

    ?>
    </div>   <!-- end modal -->   
    <?php

} // end Namespace
?>
