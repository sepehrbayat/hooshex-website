<?php
$db=new PDO("sqlite:database/database.sqlite");
$db->exec("INSERT INTO migrations (migration, batch) VALUES ('2025_12_21_140740_add_curator_media_fields_to_models', 8)");
