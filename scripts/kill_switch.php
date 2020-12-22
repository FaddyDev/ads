<?php
//create a stop.txt if it doesn't exist
if(!file_exists('stop.txt'))
{
    $kill_switch = fopen('stop.txt','w');
    fclose($kill_switch);
}
?>