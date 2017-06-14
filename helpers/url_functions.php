<?php

function checkPageExists($url)
{
    $exists = false;
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    /* Get the HTML or whatever is linked in $url. */
    curl_exec($handle);
    /* for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($httpCode != 404) {
        $exists = true;
    }
    curl_close($handle);
    return $exists;
}


function processString($data)
{
    $data = preg_replace("/\r|\n/", " ", $data);
    $data = strip_tags(trim($data));
    $data = html_entity_decode($data, ENT_QUOTES);
    $data = html_entity_decode($data);
    return $data;
}


function getHtmlContent($url)
{
    global $config;
    // echo "Getting html contents through ".$config['default_ip'].PHP_EOL;
    // $context = stream_context_create(array('socket' => array('bindto' => $config['default_ip'] . ':0')));

    // Page content of Google Books URL of the book
    // $page_content = file_get_contents($url, null, $context);
    $page_content = file_get_contents($url);
    return $page_content;
}
