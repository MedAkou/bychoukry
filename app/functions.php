<?php

function Admin(){
    return  $_SESSION['auth-admin'] ?? false;
}
