<?php
$p = new PDO('mysql:host=127.0.0.1;port=3306;dbname=bd_tiendapos', 'root', '');
$p->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== FOREIGN KEYS ===" . PHP_EOL;
$sql = "SELECT k.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME, r.DELETE_RULE, r.UPDATE_RULE
        FROM information_schema.KEY_COLUMN_USAGE k
        JOIN information_schema.REFERENTIAL_CONSTRAINTS r
          ON k.CONSTRAINT_NAME = r.CONSTRAINT_NAME
         AND k.CONSTRAINT_SCHEMA = r.CONSTRAINT_SCHEMA
        WHERE k.TABLE_SCHEMA = 'bd_tiendapos' AND k.REFERENCED_TABLE_NAME IS NOT NULL";
foreach ($p->query($sql) as $row) {
    echo "{$row['TABLE_NAME']}.{$row['COLUMN_NAME']} -> {$row['REFERENCED_TABLE_NAME']}.{$row['REFERENCED_COLUMN_NAME']} (DEL:{$row['DELETE_RULE']}, UPD:{$row['UPDATE_RULE']})" . PHP_EOL;
}

echo PHP_EOL . "=== INDICES productos ===" . PHP_EOL;
foreach ($p->query("SHOW INDEX FROM productos") as $row) {
    echo "{$row['Key_name']} col={$row['Column_name']} unique=" . ($row['Non_unique'] ? 'no' : 'yes') . PHP_EOL;
}
