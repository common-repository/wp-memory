<?php

namespace bill_banners;

/**
 * @author William Sergio Minossi
 * @copyright 26/11/2021-2023
 */
if (!defined("ABSPATH")) {
    die("Invalid request.");
}
ob_start();
// Define the expiration time for transients (1 day)
$transient_expiration = DAY_IN_SECONDS;
// Try to get the data stored in transients
$cached_news_data = get_transient('news_data');
$cached_coupon_data = get_transient('coupon_data');
//DEBUG
/*
$cached_news_data = false;
$cached_coupon_data = false;
*/
// Verifique se os transientes não existem
if ($cached_news_data === false && $cached_coupon_data === false) {
    try {
        // Define the API URL
        $url = "https://billminozzi.com/API/bill-api.php";
        // Make the POST request
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'version' => '2'
            ),
            'cookies' => array()
        ));
        // Check if there was an error in the request
        if (is_wp_error($response)) {
            // throw new \Exception($response->get_error_message());
        }
        // Retrieve the body of the response
        $response_body = wp_remote_retrieve_body($response);
        // Check if the response is not empty
        if (empty($response_body)) {
            // throw new \Exception('The API response is empty.');
        }
        // Decode the JSON response
        $data = json_decode($response_body, true);
        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            // throw new \Exception('JSON decoding error: ' . json_last_error_msg());
        }
        // Check if it's a coupon message
        if (isset($data['title']) && isset($data['code'])) {
            // Coupon data
            $sanitized_title = sanitize_text_field($data['title']);
            $sanitized_code = sanitize_text_field($data['code']);
            // Prepare coupon data
            $coupon_data = json_encode(array(
                'title' => $sanitized_title,
                'code' => $sanitized_code,
                'image' => isset($data['image']) ? sanitize_text_field($data['image']) : 'default.png',
            ));
            // Store the sanitized coupon data in transients
            set_transient('coupon_data', $coupon_data, $transient_expiration);
            // Store the coupon data in $cached_coupon_data
            $cached_coupon_data = $coupon_data;
        } elseif (isset($data['message'])) {
            // News data
            $message_text = stripslashes($data['message']);
            // Sanitize the message text
            $sanitized_message_text = wp_kses($message_text, array(
                'p' => array(),
                'b' => array(),
                'strong' => array(),
                'br' => array(),
                'a' => array(
                    'href' => array(),
                    'title' => array(),
                    'target' => array(),
                    'rel' => array()
                ),
            ));
            // Store the sanitized news data in transients
            set_transient('news_data', $sanitized_message_text, $transient_expiration);
            // Store the news data in $cached_news_data
            $cached_news_data = $sanitized_message_text;
        } else {
            // Set $cached_news_data and $cached_coupon_data to empty strings if neither case is found
            $cached_news_data = '';
            $cached_coupon_data = '';
        }
    } catch (\Exception $e) {
        // Set $cached_news_data and $cached_coupon_data to empty strings in case of an error
        $cached_news_data = '';
        $cached_coupon_data = '';
    }
}


ob_end_clean();

// Sempre exibe a seção adicional
echo '<ul>';


$x = rand(1, 2);
if ($x < 2) {
    echo '<h2>' . esc_html__("Did this plugin provide useful information?", 'wp-memory') . '</h2>';
    echo '<img src="' . esc_url(WPMEMORYIMAGES) . '/help3.jpg' . '" style="width: 100%; height: auto;" />';

    esc_html_e("Please support us by rating our plugin on WordPress.org and help us keep it alive and up to date.", 'wp-memory');
?>
    <br /><br />
    <a href="https://wordpress.org/support/plugin/wp-memory/reviews/#new-post" class="button button-medium button-primary"><?php esc_html_e('Rate or Share', 'wp-memory'); ?></a>
<?php
} else {
    echo '<h2>' . esc_html__('Can You Do Us a Favor?', 'wp-memory') . '</h2>';
    echo '<img src="' . esc_url(WPMEMORYIMAGES) . '/help1.jpg' . '" style="width: 100%; height: auto;" />';


    esc_html_e("If this plugin provide useful information, we’d be grateful if you could rate it on WordPress.org. It only takes a moment and helps us reach more users and stay motivated. Thank you!", 'wp-memory');
?>
    <br /><br />
    <a href="https://wordpress.org/support/plugin/wp-memory/reviews/#new-post" class="button button-medium button-primary"><?php esc_html_e('Rate', 'wp-memory'); ?></a>
<?php
}
echo '</ul>';

// Exibe vídeos e informações adicionais
echo '<ul>';
$x = rand(1, 3);
$url = '';
switch ($x) {
    case 1:
        $url = esc_url(WPMEMORYURL . "assets/videos/memory4.mp4");
        $title_ad = esc_attr__("Boost Your WP Memory with Our Premium Version – No Manual Changes Needed!", "wp-memory");
        break;
    case 2:
        $url = esc_url(WPMEMORYURL . "assets/videos/memory2.mp4");
        $title_ad = esc_attr__("Increase WP Memory with Our Premium Version – No Manual Edits Required!", "wp-memory");
        break;
    case 3:
        $url = esc_url(WPMEMORYURL . "assets/videos/memory3.mp4");
        $title_ad  = esc_attr__("Upgrade for Safe WP Memory Boost – Avoid Risky Manual Adjustments!", "wp-memory");
        break;
}
echo '<h2>' . esc_attr($title_ad) . '</h2>';
?>
<video id="bill-banner-2" style="margin:-20px 0px -15px -12px; padding:0px;" width="400" height="230" muted>
    <source src="<?php echo esc_url($url); ?>" type="video/mp4">
</video>
<li><?php esc_html_e("Lifetime license with premium enhancements: One-time payment of just $17.99!", "wp-memory"); ?></li>
<li><?php esc_html_e("Our Premium version can modify quickly the necessary files to increase your WP Memory Limit and PHP Memory. Don't take the risk of making manual changes that can harm your site.", "wp-memory"); ?></li>
<li><?php esc_html_e("Let your site run smoothly, free from memory issues with our plugin.", "wp-memory"); ?></li>
<li><?php esc_html_e("Dedicated Premium Support", "wp-memory"); ?></li>
<li><?php esc_html_e("More...", "wp-memory"); ?></li>
<br />
<a href="https://wpmemory.com/premium/" class="button button-medium button-primary"><?php esc_html_e('Learn More', 'wp-memory'); ?></a>
<?php
echo '</ul>';

// Exibição dos dados com prioridade para o cupom
if ($cached_coupon_data !== '' && $cached_coupon_data !== false) {
    // Handle coupon data
    $r = json_decode($cached_coupon_data, true);
    $title = sanitize_text_field($r['title']);
    $code = sanitize_text_field($r['code']);
    $image = 'coupon.gif';
    $message_text = 'Use the code: ' . $code;
    // Clean the output buffer
    // ob_end_clean();
    // Display the coupon block
    echo '<ul>';
    echo '<h2>' . esc_html($title) . '</h2>';
    echo '<img src="' . esc_url(WPMEMORYIMAGES) . '/' . esc_attr($image) . '" style="width: 100%; height: auto;" />';
    echo "<br>";
    echo '<p><h2>' . wp_kses_post($message_text) . '</h2></p>';
    echo '</ul>';
} elseif ($cached_news_data !== '') {
    // Handle news data
    // Split the message into individual news items using ' | ' as a separator
    $news_items = explode(' | ', $cached_news_data);
    // Initialize variables to store the title and message
    $title = '';
    $message_text = '';
    // Randomly select a news item
    $random_key = array_rand($news_items);
    $random_news_item = $news_items[$random_key];
    // Iterate over the selected news item and separate title and body using ' || '
    $parts = explode(' || ', $random_news_item, 2);
    if (count($parts) == 2) {
        $title = sanitize_text_field(trim($parts[0]));
        $message_text = trim($parts[1]);
    }
    // Sanitize the message text
    $message_text = wp_kses($message_text, array(
        'p' => array(),
        'b' => array(),
        'strong' => array(),
        'br' => array(),
        'a' => array(
            'href' => array(),
            'title' => array(),
            'target' => array(),
            'rel' => array()
        ),
    ));
    // Store the data in transients
    set_transient('title', $title, $transient_expiration);
    set_transient('message', $message_text, $transient_expiration);
    // Clean the output buffer
    //ob_end_clean();
    // Display the news block
    if ($title && $message_text) {
        echo '<ul>';
        echo '<h2>' . esc_html($title) . '</h2>';
        echo '<img src="' . esc_url(WPMEMORYIMAGES) . '/news.gif" style="width: 100%; height: auto;" />';
        echo "<br>";
        echo '<p>' . wp_kses($message_text, array(
            'p' => array(),
            'b' => array(),
            'strong' => array(),
            'br' => array(),
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array(),
                'rel' => array()
            ),
        )) . '</p>';
        echo '</ul>';
    }
}

?>