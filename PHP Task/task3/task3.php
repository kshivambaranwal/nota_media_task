<?php

final class TableCreator
{
    // Database connection.
    private $db;

    // constructore here for call create table and fill 
    public function __construct()
    {
        $this->db = new PDO("mysql:host=localhost;dbname=wiki", "root", "");
        if (!$this->TableExist()) {
            $this->create();
        }
        $this->fill();
    }

    // this function for exist table
    private function TableExist()
    {
        $sql = "SHOW TABLES LIKE 'Test'";
        $stmt = $this->db->query($sql);
        return $stmt->rowCount() > 0;
    }

    // create function for test table
    private function create()
    {
        $sql = "CREATE TABLE Test (
            id INT AUTO_INCREMENT PRIMARY KEY,
            script_name VARCHAR(25),
            start_time DATETIME,
            end_time DATETIME,
            result ENUM('normal', 'illegal', 'failed', 'success')
        )";

        $this->db->exec($sql);
    }

    //    this function for fill table data 
    private function fill()
    {
        for ($i = 1; $i <= 10; $i++) {
            $scriptName = $this->random_name();
            $startTime = $this->randome_date();
            $endTime = $this->randome_date();
            $result = $this->randome_result();

            $sql = "INSERT INTO Test (script_name, start_time, end_time, result) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$scriptName, $startTime, $endTime, $result]);
        }
    }

    //    get function for get data from test table
    public function get()
    {
        $sql = "SELECT * FROM Test WHERE result IN ('normal', 'success')";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // helper functions for generate randome data 
    private function random_name()
    {
        $firstNames = ['John', 'Jane', 'Peter', 'Mary', 'David'];
        $lastNames = ['Doe', 'Smith', 'Jones', 'Williams', 'Brown'];
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        return  $firstName .' '. $lastName;
    }

    private function randome_date()
    {
        return date('Y-m-d H:i:s', rand(1609459200, 1672444800));
    }

    private function randome_result()
    {
        $results = ['normal', 'illegal', 'failed', 'success'];
        return $results[array_rand($results)];
    }
}

// Example usage:
$creator = new TableCreator();
$data = $creator->get();
echo "<pre>";
print_r($data);
