<?php

include "database.php";

$data = fetchFromDatabase()["data"];
$width = array_key_exists("width", $_GET) ? $_GET["width"] : 650;
$height = array_key_exists("height", $_GET) ? $_GET["height"] : 400;
$max_value = max(array_map(function($item) { return $item[1]; }, $data))

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Graph</title>
</head>
<body>
    <h1>Graph</h1>
    <map name="graph">
        <?php foreach($data as $index => $item): ?>
            <area 
            shape="circle" 
            coords="<?= (($item[0]/count($data)) * ($width - 2 * $padding)) + $padding  ?>,
                    <?= $height - (($item[1] == -1 ? 0 : $item[1]/$max_value) * ($height - 2 * $padding) + $padding) ?>, 
                    8" 
            onclick="dotClickHandler(<?= $item[0] ?>)"
            >
        <?php endforeach; ?>
    </map>
    <div>
        <img 
        src="image.php?
        width=<?php echo $width?>&
        height=<?php echo $height?>"
        usemap="#graph" width="650px" height="400px">
    </div>
    
    <div>
        <table>
            <thead>
                <tr>
                    <th>Argument</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $item): ?>
                    <tr>
                        <td><?= $item[0] ?></td>
                        <td><?= $item[1] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

<script>
    function dotClickHandler(index){
        alert("Index: " + index);
    }
</script>