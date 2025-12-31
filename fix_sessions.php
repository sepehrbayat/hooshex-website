<?php
$db=new PDO("sqlite:database/database.sqlite");
$db->exec("DELETE FROM migrations WHERE migration='2025_12_09_135019_create_sessions_table'");
$db->exec('DROP TABLE IF EXISTS sessions');
