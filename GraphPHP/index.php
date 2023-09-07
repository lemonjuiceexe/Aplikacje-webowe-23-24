<?php
// Set width and height of the image based on url params
$width = array_key_exists("width", $_GET) ? $_GET["width"] : 500;
$height = array_key_exists("height", $_GET) ? $_GET["height"] : 200;
$canvas = imagecreatetruecolor($width, $height);
// Styles
$white = imagecolorallocate($canvas, 255, 255, 255);
$black = imagecolorallocate($canvas, 0, 0, 0);
$grey = imagecolorallocate($canvas, 128, 128, 128);
$red = imagecolorallocate($canvas, 255, 0, 0);
$green = imagecolorallocate($canvas, 24, 163, 36);
$padding = 30;
$dot_size = 8;
// Data
// null - no data, -1 - illness
$data = [[0, 6], [1, 4], [2, 7], [3, 10], [4, -1], [5, 7], [6, 6], [7, 4], [8, 5], [9, 3], [10, 2], [11, null], [12, null], [13, 4], [17, 8]];
$max_value = max(array_map(function($item) { return $item[1]; }, $data));
$steps_vertical_number = 5;
$step_vertical_value = $max_value / $steps_vertical_number;
$step_vertical_height = ($height - 2 * $padding) / $steps_vertical_number;
$steps_horizontal_number = count($data);
$step_horizontal_width = ($width - 2 * $padding) / count($data);

function drawCoordinates(){
    global $canvas, $width, $height, $padding, $black, $grey, $white, $data, $steps_vertical_number, $step_vertical_height, $step_vertical_value, $steps_horizontal_number, $step_horizontal_width;
    // Draw the background
    imagefilledrectangle($canvas, 0, 0, $width, $height, $white);
    // Axis
    imageline($canvas, $padding, $height - $padding, $width - $padding, $height - $padding, $black);
    imageline($canvas, $padding, $height - $padding, $padding, $padding, $black);
    // Values axis
    imagesetstyle($canvas, [$grey, $grey, $grey, $grey, $white, $white, $white, $white]);
    for ($i = 0; $i <= $steps_vertical_number; $i++) {
        $y = $height - $padding - $i * $step_vertical_height;
        imageline($canvas, $padding, $y, $padding - 5, $y, $black);
        // Dashed horizontal lines
        if($i > 0)
            imageline($canvas, $padding + 1, $y, $width - $padding, $y, IMG_COLOR_STYLED);
        imagestring($canvas, 3, 5, $y - 5, $i * $step_vertical_value, $black);
    }
    // Arguments axis
    for($i = 0; $i < count($data); $i++){
        $x = $padding + $i * ($width - 2 * $padding) / count($data);
        // Vertical lines
        if($i > 0)
            imageline($canvas, $x, $height - ($padding + 1), $x, $padding, IMG_COLOR_STYLED);
        imagestring($canvas, 3, $x, $height - $padding + 5, $data[$i][0], $black);
    }
}
function drawValuePoints(){
    global $canvas, $padding, $height, $dot_size, $red, $grey, $green, $data, $step_vertical_height, $step_vertical_value, $step_horizontal_width;
    for($i = 0; $i < count($data); $i++){
        $color = $red;
        $x = $padding + $i * $step_horizontal_width;
        $y = $height - $padding - ($data[$i][1] / $step_vertical_value) * $step_vertical_height;
        if($data[$i][1] == null){
            $color = $grey;
            $y = $height - $padding;
        }
        else if($data[$i][1] == -1) {
            $color = $green;
            $y = $height - $padding;
        }
        imagefilledellipse($canvas, $x, $y, $dot_size, $dot_size, $color);
    }
}
function drawLinesBetweenPoints(){
    global $data, $canvas, $height, $red, $step_vertical_value, $step_vertical_height, $step_horizontal_width, $padding;
    for($i = 0; $i < count($data); $i++){
        if($i > 0){
            $x1 = $padding + ($i - 1) * $step_horizontal_width;
            $y1 = $height - $padding - ($data[$i - 1][1] / $step_vertical_value) * $step_vertical_height;
            $x2 = $padding + ($i) * $step_horizontal_width;
            $y2 = $height - $padding - ($data[$i][1] / $step_vertical_value) * $step_vertical_height;
            if($data[$i - 1][1] != null && $data[$i][1] != null && $data[$i - 1][1] != -1 && $data[$i][1] != -1){
                imageline($canvas, $x1, $y1, $x2, $y2, $red);   
            }
        }
    }
}

drawCoordinates();
drawValuePoints();
drawLinesBetweenPoints();

header("Content-Type: image/png");
imagepng($canvas);
?>