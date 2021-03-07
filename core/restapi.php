<?php
// RestAPI

class ApiKeys extends PDO {

    private $table_name = NULL;

    public function __construct($config = NULL) {

        $this->table_name = strtolower(get_class($this));

        if($config == NULL) {

            echo json_encode(array(
                'Error' => 'Config is empty or missing',
            ));

            die();
        }

        try {

            parent::__construct("mysql:host=localhost;dbname=" . $config['db'], $config['user'], $config['pwd'], array(PDO::MYSQL_ATTR_INIT_COMMAND =>  "SET NAMES UTF8"));

        } catch (PDOException $e) {

            echo json_encode(array(
                'Error' => 'Db connection error: ' . $e->getMessage()
            ));

            die();
        }

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


    }

    public function check_key($key = NULL) {

            if($key == NULL) {

                return false;

            } else {

                $key = htmlspecialchars(strip_tags($key));

            }

            try {

                $sql = "SELECT * FROM `" . $this->table_name . "` WHERE `active` = 1";

                $sth = $this->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                
                $sth->execute();
                
                $ret = FALSE;

                while($row = $sth->fetch(PDO::FETCH_ASSOC)){

                    extract($row);

                    $salted = sha1($key . $salt);

                    if($salted == $api_key) {

                        $ret = TRUE;

                    }

                }

            } catch (PDOExeption $e) {

                echo json_encode(array(
                    'Error' => 'Db connection error: ' . $e->getMessage()
                ));
    
                die();

            }

            return $ret;

    }

}