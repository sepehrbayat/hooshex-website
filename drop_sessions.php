<?php
$db=new PDO("sqlite:database/database.sqlite");
$db->exec('DROP TABLE IF EXISTS sessions');
