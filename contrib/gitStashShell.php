#!/usr/bin/env php
<?php

// Make sure only SSH connections are served
if (! isset($_SERVER['SSH_CONNECTION'])) {
    print "Only SSH connections are allowed.";
    exit(1);
}

// Sanity check on given command line
if ($_SERVER['argc'] != 3 || $_SERVER['argv'][1] != '-c') {
    print "This account is only used for git activity. Shell login is not permitted.";
    exit(1);
}

// GITSTASH_USER environment must be set from the authorized_keys file or AuthorizedKeysCommand application.
if (! isset($_SERVER['GITSTASH_USER'])) {
    print "It seems I cannot figure out who you are.";
    exit(1);
}

// List of commands that are allowed access.
$allowed_commands = array(
    'git-receive-pack',
    'git-upload-pack',
    'git-upload-archive',
);

// Make sure "git foo" is seen as "git-foo". Most likely for older git clients.
if (substr($_SERVER['argv'][2], 0, 4) == 'git ') {
    $_SERVER['argv'][2][3] = '-';
}

// Parse command line arguments
preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $_SERVER['argv'][2], $matches);
$git_args = $matches[0];

// Validate command
if (! in_array($git_args[0], $allowed_commands)) {
    print "Incorrect git command.";
    exit(1);
}

// @TODO: Here be permission checks


// Escape shell command and arguments (what could possibly go wrong)
$cmd = escapeshellcmd($git_args[0]);
array_shift($git_args);
array_walk($git_args, function($e) { return escapeshellarg($e); } );

// Passthru information
$cmdline = $cmd . ' ' . join(' ', $git_args);
passthru($cmdline, $status);

exit($status);
