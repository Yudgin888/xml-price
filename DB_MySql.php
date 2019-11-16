<?php

class DB_MySql
{
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $mysqli;

    public function __construct($host, $user, $pass, $dbname)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
        $this->init();
    }

    private function init()
    {
        if (!$this->mysqli || $this->mysqli->stat() == null) {
            $this->mysqli = new \mysqli($this->host, $this->user, $this->pass, $this->dbname);
            if ($this->mysqli->connect_errno) {
                $this->mysqli->close();
                die('DB connect error! ' . $this->mysqli->connect_error);
            }
            $this->create_table();
        }
    }

    public function execute($sql)
    {
        if (!$this->mysqli || $this->mysqli->stat() == null) {
            $this->init();
        }
        if ($this->mysqli->connect_errno) {
            $this->mysqli->close();
            die('DB connect error! ' . $this->mysqli->connect_error);
        }
        if (!$result = $this->mysqli->query($sql, MYSQLI_USE_RESULT)) {
            $this->mysqli->close();
            die('DB error! ' . $this->mysqli->connect_error);
        }
        $res = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $res;
    }

    public function prepare_execute($sql, $types, $params)
    {
        if (!$this->mysqli || $this->mysqli->stat() == null) {
            $this->init();
        }
        if ($this->mysqli->connect_errno) {
            $this->mysqli->close();
            die('DB connect error! ' . $this->mysqli->connect_error);
        }
        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            die("DB prepare error! (" . $this->mysqli->errno . ") " . $this->mysqli->error);
        }
        $stmt->bind_param($types, ...$params);
        $res_execute = $stmt->execute();
        $result = $stmt->get_result();
        if($result) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $stmt->close();
            return $data;
        }
        $stmt->close();
        return $res_execute;
    }

    public function get_insert_id(){
        return $this->mysqli->insert_id;
    }

    public function close()
    {
        $this->mysqli->close();
    }

    private function create_table()
    {
        if (!$result = $this->mysqli->query("SHOW TABLES LIKE 'vendor';")) {
            $this->mysqli->close();
            die('DB error! ' . $this->mysqli->connect_error);
        } else if (!$res = $result->fetch_assoc()) {
            $sql_vendor = "CREATE TABLE vendor (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` TEXT NOT NULL,
                PRIMARY KEY (id));";
            if (!$result = $this->mysqli->query($sql_vendor)) {
                $this->mysqli->close();
                die('DB error! ' . $this->mysqli->connect_error);
            }
        }

        if (!$result = $this->mysqli->query("SHOW TABLES LIKE 'product';")) {
            $this->mysqli->close();
            die('DB error! ' . $this->mysqli->connect_error);
        } else if (!$res = $result->fetch_assoc()) {
            $sql_product = "CREATE TABLE product (
                `id` INT NOT NULL AUTO_INCREMENT,
                `id_vendor` INT NOT NULL,
                `name` TEXT NOT NULL,
                PRIMARY KEY (id));";
            if (!$result = $this->mysqli->query($sql_product)) {
                $this->mysqli->close();
                die('DB error! ' . $this->mysqli->connect_error);
            }
        }

        if (!$result = $this->mysqli->query("SHOW TABLES LIKE 'price';")) {
            $this->mysqli->close();
            die('DB error! ' . $this->mysqli->connect_error);
        } else if (!$res = $result->fetch_assoc()) {
            $sql_price = "CREATE TABLE price (
                `id` INT NOT NULL AUTO_INCREMENT,
                `id_product` INT NOT NULL,
                `price` double(10, 6),
                `currency` varchar(5),
                PRIMARY KEY (id));";
            if (!$result = $this->mysqli->query($sql_price)) {
                $this->mysqli->close();
                die('DB error! ' . $this->mysqli->connect_error);
            }
        }
    }
}