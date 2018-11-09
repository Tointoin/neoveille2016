<?php

/* $Id$ */
$ini_array = parse_ini_file("/opt/neoveille-backend/config.cfg", True);

$host       = $ini_array['mysql']['host'];
$usermysql  = $ini_array['mysql']['user'];
$passmysql  = $ini_array['mysql']['password'];
$passmysl   = $ini_array['mysql']['password'];

$datatables = $ini_array['db']['datatables'];
$rssdata    = $ini_array['db']['rssdata'];
$neo3       = $ini_array['db']['neo3'];

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}
?>
