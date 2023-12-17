<?php
$memcache = new Memcache();
$memcache->connect('localhost', 11211) or die ("Could not connect");
function get_messages(){
    global $memcache;
    return $memcache->get('messages') ? $memcache->get('messages') : [];
}

$messages_on_request = get_messages();

$start_time = time();
$duration = 5;

// For 5 seconds, check for new messages every half a second
while(time() < $start_time + $duration){
    usleep(500000);
    if($messages_on_request != get_messages()){
        $messages_on_request = get_messages();
        break;
    }
}

echo json_encode($messages_on_request);
?>
