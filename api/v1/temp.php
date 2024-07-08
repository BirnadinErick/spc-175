<?php

try {

    $databasePath = __DIR__ . '/dev.bk.sqlite';
    $pdo = new PDO("sqlite:$databasePath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $pdo->query("select
	DISTINCT path,
	         data,
	6 as updated_by,
	'2024-07-08' as updated_at,
	uid,
	'' as meta
from
	contents
order by
	updated_at DESC;");
    $results = $stmt->fetchAll();

    // -------------------------------------------------------

    $dsn = "mysql:host=localhost;dbname=spc;charset=utf8mb4";
    $pdo_2 = new PDO($dsn, '', '');
    $pdo_2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo_2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    foreach ($results as $row) {
//        $data = bzdecompress($row["data"]);

        $placeholders = ":" . implode(", :", array_keys($row));
        $columns = implode(", ", array_keys($row));
        $sql = "INSERT INTO contents ($columns) VALUES ($placeholders)";
        $stmt = $pdo_2->prepare($sql);

        foreach ($row as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        echo $row['path'] . '<br/>';
    }

} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}

