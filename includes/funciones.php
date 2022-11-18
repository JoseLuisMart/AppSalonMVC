<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo($actual, $proximo) : bool {

    if($actual !== $proximo) {
        return true;
    }

    return false;
}

// FUncion que revisa que el usuario este autenticado

function isAuth() : void {
    if(!isset($_SESSION['login'])) {
        header('Location: /');
    }
}


// FUncion que revisa que el usuario sea administrador

function isAdmin() : void {
    if(!isset($_SESSION['admin'])) {
        header('Location: /');
    }
}