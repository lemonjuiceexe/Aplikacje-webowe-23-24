<?php
$memcache = new Memcache();
$memcache->connect('localhost', 11211) or die ("Could not connect");
function get_messages(){
    global $memcache;
    return $memcache->get('messages') ? $memcache->get('messages') : [];
}
header('Content-Type: application/json');

echo json_encode(get_messages());
die();
?>