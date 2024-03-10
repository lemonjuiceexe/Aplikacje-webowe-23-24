<?php
include "init.php";

$language = $_GET["language"];
putenv("LANG=" . $language);
setlocale(LC_ALL, $language);

bindtextdomain("messages", "locale");
bind_textdomain_codeset("messages", 'UTF-8');
textdomain("messages");

$translations = array(
    "Hi" => _("Hi"),
    "You're in lobby" => _("You're in lobby"),
    "Username" => _("Username"),
    "Join lobby" => _("Join lobby"),
    "Winner of the last game" => _("Winner of the last game"),
    "Leave lobby" => _("Leave lobby"),
    "Players in lobby" => _("Players in lobby"),
    "Ready" => _("Ready"),
    "Unready" => _("Unready"),
    "Not ready" => _("Not ready"),
    "Time left" => _("Time left"),
    "Click me to roll" => _("Click me to roll"),
    "Value" => _("Value"),
);

/*
if($language == "pl_PL"){
    $translations = array(
        'Hi' => 'Cześć',
        "You're in lobby" => "Jesteś w pokoju",
        "Username" => "Nazwa użytkownika",
        "Join lobby" => "Dołącz do pokoju",
        "Winner of the last game" => "Zwycięzca ostatniej gry",
        "Leave lobby" => "Opuść pokój",
        "Players in lobby" => "Gracze w pokoju",
        "Ready" => "Gotowy",
        "Unready" => "Niegotowy",
        "Not ready" => "Niegotowy",
        "Time left" => "Pozostały czas",
        "Click me to roll" => "Naciśnij mnie żeby rzucić kostką",
        "Value" => "Wartość kostki",
    );
} else if($language == "en_US"){
    $translations = array(
        'Hi' => 'Hi',
        "You're in lobby" => "You're in lobby",
        "Username" => "Username",
        "Join lobby" => "Join lobby",
        "Winner of the last game" => "Winner of the last game",
        "Leave lobby" => "Leave lobby",
        "Players in lobby" => "Players in lobby",
        "Ready" => "Ready",
        "Unready" => "Unready",
        "Not ready" => "Not ready",
        "Time left" => "Time left",
        "Click me to roll" => "Click me to roll",
        "Value" => "Value",
    );
} */

echo json_encode($translations);
