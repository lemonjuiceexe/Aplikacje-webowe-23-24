<?php

include "setup.php";

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
    <style>
        dialog{
            position: fixed;
            width: 200px;
            /* left: 50%; */
            /* top: 50%; */
            /* transform: translate(-50%, -50%); */
        }
        dialog form{
            height: 200px;
            display: flex;
            flex-flow: column nowrap;
            align-items: center;
            justify-content: space-around;
        }
    </style>
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
            onclick="dotClickHandler(<?= $item[0] ?>, <?= $item[1] ?>)"
            >
        <?php endforeach; ?>
    </map>
    <div>
        <img 
        src="image.php?
        width=<?php echo $width?>&
        height=<?php echo $height?>"
        usemap="#graph" width="<?php echo $width?>" height="<?php echo $height?>"
        onload="calculateMap()"
        >
    </div>
    <dialog>
        <form> <!--action="update.php" method="POST"-->
            <label for="index" class="dialog-title">Record no. </label>
            <input type="hidden" name="id" value="">
            <input type="number" name="value" id="index" step="1">
            <button type="button" value="Submit" onclick="fetchNumber()">Submit</button>
            <button type="button" name="No data" value="No data" onclick="fetchND()">No data</button>
            <button type="button" name="Illness" value="Illness" onclick="fetchIll()">Illness</button>
            <button type="button" onclick="document.querySelector('dialog').close()">Cancel</button>
        </form>
    </dialog>
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
    function calculateMap(){
        fetch("get_db_data.php", {
            method: "POST"
        })
        .then(response => response.json())
        .then(response => {
            console.log(response);
            // Generate the map areas based on the data in response
            let map = document.querySelector("map");
            map.innerHTML = "";
            let data = response.data;
            let width = <?php echo $width ?>;
            let height = <?php echo $height ?>;
            let padding = <?php echo $padding ?>;
            let max_value = <?php echo $max_value ?>;

            data.forEach((item, index) => {
                let area = document.createElement("area");
                area.shape = "circle";
                area.coords = `${((item[0]/data.length) * (width - 2 * padding)) + padding},
                                ${height - ((item[1] == -1 ? 0 : item[1]/max_value) * (height - 2 * padding) + padding)}, 
                                8`;
                area.onclick = () => dotClickHandler(item[0], item[1]);
                map.appendChild(area);
            });
        });
    }

    function dotClickHandler(index, value){
        document.querySelector("dialog").showModal();
        document.querySelector("input[name='value']").value = value;
        document.querySelector(".dialog-title").innerText = "Record no. " + index;
        document.querySelector("input[name='id']").value = index;
    }
    
    function fetchNumber(){
        let index = document.querySelector("input[name='id']").value;
        let value = document.querySelector("input[name='value']").value;
        console.log("Read value ", value, " ", isNaN(value));
        if(isNaN(value) || value == ""){
            document.querySelector("dialog").close();
            return;
        } 
        console.log(index, value);
        fetch(`update.php?id=${index}&value=${value}`, {
            method: "GET"
        })
        .then(response => {
            document.querySelector("img").src = "image.php?width=<?php echo $width?>&height=<?php echo $height?>" + "&" + new Date().getTime();
        })
        .finally(() => { 
            document.querySelector("dialog").close();
        });
    }
    function fetchND(){
        let index = document.querySelector("input[name='id']").value;
        fetch(`update.php?id=${index}&no_data=1`, {
            method: "GET"
        })
        .then(response => {
            document.querySelector("dialog").close();
            document.querySelector("img").src = "image.php?width=<?php echo $width?>&height=<?php echo $height?>" + "&" + new Date().getTime();
        });
    }
    function fetchIll(){
        let index = document.querySelector("input[name='id']").value;
        fetch(`update.php?id=${index}&illness=1`, {
            method: "GET"
        })
        .then(response => {
            document.querySelector("dialog").close();
            document.querySelector("img").src = "image.php?width=<?php echo $width?>&height=<?php echo $height?>" + "&" + new Date().getTime();
        });
    }
</script>