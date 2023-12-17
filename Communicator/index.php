<?php 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        <?php include './style.css'; ?>
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="history">
            <!-- <?php 
                include './server.php';

                foreach($messages_on_request as $message){
                    echo "<div class='message'>";
                    echo "<div class='message-header'>";
                    echo "<p class='message-author'>" . $message["user"] . "</p>";
                    echo "<p class='message-timestamp'>" . date("d/m/y H:i", $message["timestamp"]) . "</p>";
                    echo "</div>";
                    echo "<p class='message-content'>" . $message["message"] . "</p>";
                    echo "</div>";
                }
            ?> -->
        </div>
        <div class="entry">
            <input type="text" name="" class="entry-input">
            <button class="entry-submit">Send</button>
        </div>
    </div>
</body>
</html>

<script>
        <?php include './script.js'; ?>
</script>