<?php

$memcache = new Memcache();
$memcache->connect('localhost', 11211) or die ("Could not connect");
$data = $memcache->get('data');

$start_time = time();
$duration = 5;

$data_on_request = $data;
// For 5 seconds, check for new messages every half a second
while(time() < $start_time + $duration){
    usleep(500000);
    if($data != $data_on_request)
        break;

    $data[] = "new message"; // Testing purposes
}

echo json_encode($memcache->getVersion());

?>
