<?php

    if(!file_exists('servers.json')){
        define('SERVER_CONFIG', json_decode(file_get_contents('servers.default.json'), true));
    } else {
        define('SERVER_CONFIG', json_decode(file_get_contents('servers.json'), true));
    }


?>