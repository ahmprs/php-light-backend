<?php
session_start();

function auth($access_level)
{
    if (!isset($_SESSION['user_id'])) return 0;
    if ($_SESSION['user_id'] == "") return 0;

    if (!isset($_SESSION['user_access_level'])) return 0;
    if ($_SESSION['user_access_level'] == "") return 0;

    if ($access_level >= $_SESSION['user_access_level']) return 1;
    else return 0;
}

function getUserId()
{
    if (!isset($_SESSION['user_id'])) return 0;
    return $_SESSION['user_id'];
}

function getUserName()
{
    if (!isset($_SESSION['user_name'])) return "";
    return $_SESSION['user_name'];
}

function getUserAccessLevel()
{
    if (!isset($_SESSION['user_access_level'])) return "";
    return $_SESSION['user_access_level'];
}

function getUserActive()
{
    if (!isset($_SESSION['user_access_active'])) return "";
    return $_SESSION['user_access_active'];
}
