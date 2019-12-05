<?php
require 'bootstrap.php';
$statement = <<<EOS
    CREATE TABLE IF NOT EXISTS sale (
        id INT NOT NULL AUTO_INCREMENT,
        paymentId CHAR(36) NOT NULL,
        customerName VARCHAR(100) NOT NULL,
        total INT NOT NULL,
        PRIMARY KEY (id)
    );
    INSERT INTO sale
        (id, paymentId, customerName, total)
    VALUES
        (1, '1abc7c83-d2c8-492f-bbde-51915cb2a589', 'Eliezer Martins', 100),
        (2, '34be5970-a9d6-4bb9-b706-162974166178', 'David Hansson', 450),
        (3, 'd6ac97d2-c336-4c84-bd18-8732008fa671', 'Dan Abramov', 342),
        (4, '9b278b70-bf12-4407-afdb-7f4ba00459f7', 'Rasmus Lerdorf', 877),
        (5, '7b89bbd2-ad19-444c-92e0-4cc0a52b3f68', 'James Gosling', 645);
EOS;
try {
    $createTable = $dbConnection->exec($statement);
    echo "Database has been sucessfully seeded!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
?>