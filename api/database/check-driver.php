<?php
if (extension_loaded('pdo_mysql')) {
    echo "PDO MySQL driver is installed and loaded.";
} else {
    echo "PDO MySQL driver is not installed or not loaded.";
}

if (extension_loaded('pdo_pgsql')) {
    echo "PDO PostgreSQL driver is installed and loaded.";
} else {
    echo "PDO PostgreSQL driver is not installed or not loaded.";
}
?>


//domain/api/database/check-driver.php

// MySQL
// no POSTGRESQL