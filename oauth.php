<?php

header('Content-type: text/json');

include 'config.php';

echo Auth::authByVkId($_GET['user_id'], $_GET['access_token']);