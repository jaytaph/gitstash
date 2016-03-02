#!/usr/bin/env php
<?php

// Ok.. this is bad... :(
$mysqli = mysqli_init();
$mysqli->real_connect('127.0.0.1', 'gitstash', 'gitstash', 'gitstash');

// We should get the fingerprint from the commandline, so we can query on that. This way, we don't have to send over
// ALL keys to SSH, only the relevant key.
$result = $mysqli->query("SELECT u.username, k.sshkey FROM authorized_keys k LEFT JOIN user u on k.user_id = u.id");
foreach ($result as $row) {
    // Add environment setting with username, so we can do permission checks in gitstash-shell
    printf('environment="GITSTASH_USER=%s" ssh-rsa %s', $row['username'], $row['sshkey']);
}

$mysqli->close();
