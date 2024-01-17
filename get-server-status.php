<?php

    require 'config.php';

    /**
     * Initialize and return a cURL handle
     */
    function initCurl($protocol, $host, $port) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $protocol . '://' . $host . ':' . $port,
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
        $curl = initCurl($value['ssl'] ? 'https' : 'http', $value['host'], $value['port']);
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
                : ($statusCode === 302 ? 'warning' : 'danger');

            $statuses[$key] = [
                'ssl' => SERVER_CONFIG[$key]['ssl'],
                'host' => SERVER_CONFIG[$key]['host'],
                'port' => SERVER_CONFIG[$key]['port'],
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

    // Order statuses by status
    usort($statuses, function($a, $b) {
        return $a['status'] === $b['status']
            ? 0
            : ($a['status'] === 'success'
                ? -1
                : ($a['status'] === 'warning'
                    ? ($b['status'] === 'success' ? 1 : -1)
                    : 1));
    });

    // Return statuses
    echo json_encode($statuses);

?>
