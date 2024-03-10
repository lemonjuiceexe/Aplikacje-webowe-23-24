<?php
Header("Content-Type: text/html");

$language = "pl_PL";
putenv("LANG=" . $language);
putenv("LANGUAGE=".$language);
putenv("LC_ALL=".$language);
echo setlocale(LC_ALL, $language);

echo bindtextdomain("messages", "locale");
bind_textdomain_codeset("messages", 'UTF-8');

echo "<br>";
textdomain("messages");
echo "sranie<br>";
echo _('Hi');



echo "<br><br>";

echo ResourceBundle::getLocales('');
