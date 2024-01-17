<?php

    require 'config.php';

    /**
     * Initialize and return a cURL handle
     */
    function initCurl($protocol, $host, $port, $path) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $protocol . '://' . $host . ':' . $port . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        return $curl;
    }

    // Initialize multi-curl
    $multiCurl = curl_multi_init();
    $curlHandles = [];
    $startTimes = [];

    foreach (SERVER_CONFIG as $key => $value) {
        $curl = initCurl($value['ssl'] ? 'https' : 'http', $value['host'], $value['port'], $value['path'] ?? '');
        $startTimes[$key] = microtime(true);
        $curlHandles[$key] = $curl;
        curl_multi_add_handle($multiCurl, $curl);
    }

    // Execute all queries simultaneously
    do {
        while (($execrun = curl_multi_exec($multiCurl, $running)) == CURLM_CALL_MULTI_PERFORM);
        if ($execrun != CURLM_OK) break;

        // a request was just completed -- find out which one and get the details
        while ($done = curl_multi_info_read($multiCurl)) {
            $info = curl_getinfo($done['handle']);
            $error = curl_error($done['handle']);
            $key = array_search($done['handle'], $curlHandles, true);

            $statusCode = $info['http_code'];
            $responseTime = microtime(true) - $startTimes[$key];
            $status = $statusCode === SERVER_CONFIG[$key]['expectedStatusCode']
                ? 'success'
                : (in_array($statusCode, [302, 401]) ? 'warning' : 'danger');

            $statuses[$key] = [
                'ssl' => SERVER_CONFIG[$key]['ssl'],
                'host' => SERVER_CONFIG[$key]['host'],
                'port' => SERVER_CONFIG[$key]['port'],
                'path' => SERVER_CONFIG[$key]['path'] ?? '',
                'status' => $status,
                'name' => $key,
                'statusCode' => (int)$statusCode,
                'statusMessage' => $error,
                'responseTime' => number_format($responseTime, 3),
            ];

            curl_multi_remove_handle($multiCurl, $done['handle']);
            curl_close($done['handle']);
        }

        if ($running) {
            curl_multi_select($multiCurl);
        }

    } while ($running);

    curl_multi_close($multiCurl);

    usort($statuses, function($a, $b) {
        // First, compare by status
        if ($a['status'] === $b['status']) {
            // If statuses are equal, order naturally by name
            return strnatcasecmp($a['name'], $b['name']);
        } else {
            // Priority order for statuses
            if ($a['status'] === 'success') {
                return -1;
            } elseif ($b['status'] === 'success') {
                return 1;
            } elseif ($a['status'] === 'warning') {
                return -1;
            } elseif ($b['status'] === 'warning') {
                return 1;
            } else {
                return 1;
            }
        }
    });
    

    // Return statuses
    echo json_encode($statuses);

?>
