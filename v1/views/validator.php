<?php

// Assuming you already have a PDO connection script or similar

function validateAdmc($dbname, $conn) {
    $pdo = $conn;

    // Connect to the specified database
    $pdo->exec("USE $dbname");

    $errors = [];

    // Check for the common tables
    $requiredTables = ['admin', 'admin_auth'];
    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);

        if (!$stmt->fetch()) {
            $errors[] = "<p style='color:red'>Table $table is missing.</p>";
        }
    }

    // Validate table and column naming conventions
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM $table");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Common columns validation
        $commonColumns = ['id', 'hash_id', 'date_created', 'time_created', 'created_by'];
        foreach ($commonColumns as $col) {
            $foundColumn = array_search($col, array_column($columns, 'Field'));
            if ($foundColumn === false) {
                $errors[] = "<p style='color:red'>Column $col is missing from table $table.</p>";
            }
        }

        // Specific data type validations
        foreach ($columns as $column) {
            if ($column['Field'] == 'date_created' && strtolower($column['Type']) !== 'date') {
                $errors[] = "<p style='color:red'>Column date_created in table $table should be of type 'date'.</p>";
            }

            if ($column['Field'] == 'time_created' && strtolower($column['Type']) !== 'time') {
                $errors[] = "<p style='color:red'>Column time_created in table $table should be of type 'time'.</p>";
            }

            if ($column['Field'] == 'hash_id' && (stripos($column['Type'], 'varchar') === false || !preg_match('/varchar\((\d+)\)/i', $column['Type'], $matches) || (int)$matches[1] < 199)) {
                $errors[] = "<p style='color:red'>Column hash_id in table $table should be of type 'varchar' with minimum length of 199.</p>";
            }
        }

        // Additional prefix validations
        foreach ($columns as $col) {
            // Validation for select_ and add_ columns
            if (strpos($col['Field'], 'select_') === 0) {
                $relatedTable = 'selection_' . substr($col['Field'], 7);
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$relatedTable]);
                if (!$stmt->fetch()) {
                    $errors[] = "<p style='color:red'>Column ".$col['Field']." in $table suggests the existence of table $relatedTable, but it's missing.</p>";
                }
            }
            if (strpos($col['Field'], 'add_') === 0) {
                $relatedTable = 'addition_' . substr($col['Field'], 4);
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$relatedTable]);
                if (!$stmt->fetch()) {
                    $errors[] = "<p style='color:red'>Column ".$col['Field']." in $table suggests the existence of table $relatedTable, but it's missing.</p>";
                }
            }

            // Validation for bool_ prefix
            if (strpos($col['Field'], 'bool_') === 0) {
                $relatedBoolKey = 'boolkey_' . substr($col['Field'], 5);
                $foundBoolKey = array_search($relatedBoolKey, array_column($columns, 'Field'));
                if ($foundBoolKey === false) {
                    $errors[] = "<p style='color:red'>Column {$col['Field']} in $table suggests the existence of column $relatedBoolKey, but it's missing.</p>";
                } else {
                    // $keyParts = explode('_', $relatedBoolKey);
                    // if (count($keyParts) < 3) {
                    //     $errors[] = "<p style='color:red'>Column $relatedBoolKey in $table should have minimum 2 keys separated by underscores.</p>";
                    // }
                }
            }

            // Validation for addition_ prefix
            if (strpos($col['Field'], 'add_') === 0) {
                $relatedTable = 'addition_' . substr($col['Field'], 4);
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$relatedTable]);
                if ($stmt->fetch()) {
                    $stmt = $pdo->prepare("SHOW COLUMNS FROM $relatedTable WHERE Field IN ('tb', 'tb_link')");
                    $stmt->execute();
                    $additionColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    if (!in_array('tb', $additionColumns) || !in_array('tb_link', $additionColumns)) {
                        $errors[] = "<p style='color:red'>Table $relatedTable should have both 'tb' and 'tb_link' columns.</p>";
                    } else {
                        // Check tb_link values exist as hash_id in the source table
                        $stmt = $pdo->prepare("SELECT DISTINCT tb_link FROM $relatedTable");
                        $stmt->execute();
                        $tbLinks = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($tbLinks as $tbLink) {
                            $stmt = $pdo->prepare("SELECT hash_id FROM $table WHERE hash_id = ?");
                            $stmt->execute([$tbLink]);
                            if (!$stmt->fetch()) {
                                $errors[] = "<p style='color:red'>Value $tbLink in $relatedTable 'tb_link' doesn't exist as 'hash_id' in $table.</p>";
                            }
                        }
                    }
                }
            }
        }
    }
    return $errors;
}

function validateAdmcPages($dbname, $conn) {
    $pdo = $conn;

    // Connect to the specified database
    $pdo->exec("USE $dbname");

    $errors = [];

    // Fetch all tables with the 'pages_' prefix
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'pages_%'");
    $stmt->execute();
    $pageTablesList = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($pageTablesList)) {
        $errors[] = "<p style='color:red'>No tables with 'pages_' prefix found.</p>";
    } else {
        foreach ($pageTablesList as $pageTable) {
            // Check for the necessary columns in each 'pages_' table
            $stmt = $pdo->query("SHOW COLUMNS FROM $pageTable");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (!in_array('name', $columns)) {
            $errors[] = "<p style='color:red'>The table '$pageTable' is missing the 'name' column.</p>";
        }

        if (!in_array('page', $columns)) {
            $errors[] = "<p style='color:red'>The table '$pageTable' is missing the 'page' column.</p>";
        }

        if (in_array('page', $columns)) {
            // Validate the tables inside the 'page' column of each 'pages_' table
            $stmt = $pdo->query("SELECT DISTINCT page FROM $pageTable");
            $linkedTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($linkedTables as $linkedTable) {
                $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$linkedTable]);
                if (!$stmt->fetch()) {
                    $errors[] = "<p style='color:red'>The table '$linkedTable' specified in the '$pageTable' 'page' column doesn't exist.</p>";
                }
            }
        }
        }
    }

    return $errors;
}


$dbname = $_GET['dbname']; // You should sanitize this input
$errors = validateAdmc($dbname,$conn);
$errors2 = validateAdmcPages($dbname,$conn);

$errors = array_merge($errors, $errors2);

if (empty($errors)) {
    echo "<p style='color:green'>Database $dbname is well-configured for ADMC.</p>";
} else {
    echo "<p style='color:red'>Errors found in database $dbname configuration:<p><br>";
    echo implode("<br>", $errors);
}