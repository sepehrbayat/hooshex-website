<?php
$db=new PDO("sqlite:database/database.sqlite");
$tables=$db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
echo "\nSessions columns:\n";
foreach($db->query("PRAGMA table_info('sessions')") as $row){echo $row['name']."\n";}
