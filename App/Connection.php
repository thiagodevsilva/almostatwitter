<?php

    namespace App;

    class Connection {

        public static function getDb() {
            try {

                $conn = new \PDO(
                    "mysql:host=localhost;dbname=devthiagosilva;charset=utf8",
                    "sa",
                    "M@sterkey"
                );

                return $conn;

            } catch (\PDOException $e) {
                //.. tratativas ..//
                echo $e->getMessage();
            }
        }
    }

?>