<?php
function sar_custom_curl_timeout( $handle ){
    curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 120 );
    curl_setopt( $handle, CURLOPT_TIMEOUT, 120 );
}

function sar_custom_http_request_timeout( $timeout_value ) {
    return 120; // 30 seconds.
}

function sar_custom_http_request_args( $r ){
    $r['timeout'] = 120;
    return $r;
}

function setup_http_requests() {
    // Setting a custom timeout value for cURL. Using a high value for priority to ensure the function runs after any other added to the same action hook.
    add_action('http_api_curl', 'sar_custom_curl_timeout', 9999, 1);

// Setting custom timeout for the HTTP request
    add_filter( 'http_request_timeout', 'sar_custom_http_request_timeout', 9999 );

// Setting custom timeout in HTTP request args
    add_filter('http_request_args', 'sar_custom_http_request_args', 9999, 1);
}
