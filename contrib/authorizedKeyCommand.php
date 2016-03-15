#!/usr/bin/env php
<?php

$configFile = __DIR__ . "/credentials.php";
if (file_exists($configFile)) {
    include_once($configFile);
}

$mysqli = mysqli_init();
$mysqli->real_connect(GITSTASH_DB_HOST, GITSTASH_DB_USER, GITSTASH_DB_PASS, GITSTASH_DB_NAME);

// If there is a fingerprint set, return only the fingerprint
if (isset($argv[2])) {
    $fingerprint = str_replace(":", "", $argv[2]);
} else {
    $fingerprint = false;
}

$query = "SELECT u.username, k.sshkey FROM authorized_keys k LEFT JOIN user u on k.user_id = u.id";
if ($fingerprint) {
    $query .= " WHERE k.fingerprint LIKE ".mysql_real_escape_string($fingerprint);
}

$result = $mysqli->query($query);
foreach ($result as $row) {
    // Add environment setting with username, so we can do permission checks in gitstash-shell
    printf('environment="GITSTASH_USER=%s" ssh-rsa %s', $row['username'], $row['sshkey']);
}

$mysqli->close();


// And if there is an authorized_keys file, add this too
if (isset($argv[1])) {
    $homeDir = posix_getpwuid($argv[1]);
    if ($homeDir) {
        $content = file_get_contents($homeDir . "/.ssh/authorized_keys");
        print $content;
    }
}
