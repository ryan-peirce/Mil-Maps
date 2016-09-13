<?php 
include 'converter.php';
$origLat = $_POST['lat']; 
$origLng = $_POST['lng']; 
$test = new Earth;
$grid1 = $test->LLtoUSNG($origLat, $origLng, 5);
echo $grid1; ?>