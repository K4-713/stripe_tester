<?php
//get the config vars
require_once 'config.php';

$public_key = $test_keys['public'];
if ($use_prod_keys) {
    $public_key = $prod_keys['public'];
}

//$post_url is the config var we want to post to. 