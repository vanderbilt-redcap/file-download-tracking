<?php
$field = $_POST['fields'];
$pid = $_GET['pid'];

$Proj = new \Project($pid);
$event_id = $Proj->firstEventId;
$recordUp = array();
$recordUp[$recordpdf][$event_id]['$field'] = "1";
$results = \Records::saveData($pid, 'array', $recordUp,'overwrite', 'YMD', 'flat', '', true, true, true, false, true, array(), true, false);
\Records::addRecordToRecordListCache($pid, $record, 1);

echo json_encode();
?>