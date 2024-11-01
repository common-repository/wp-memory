<?php
/**
 * @ Author: Bill Minozzi
 * @ Copyright: 2020 www.BillMinozzi.com
 * @ Modified time: 2021-03-02 12:33:13
 */
if (!defined('ABSPATH')) {
    die('We\'re sorry, but you can not directly access this file.');
}

echo '<div class="wrap-wpmemory ">' . "\n";



echo '<h2 class="title">useful free plugins from the same Author</h2>' . "\n";

if(!is_multisite())
   wpmemory_new_more_plugins();
else
{

    ?>
    <script>
    window.location.href = 'https://siterightaway.net/freebies/';
    </script>
    <?php


}




echo '</div>';
