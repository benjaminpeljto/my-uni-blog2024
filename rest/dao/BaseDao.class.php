<?php

require_once __DIR__ . '/../Config.class.php';

class BaseDao
{
    protected $conn;
    protected $table_name;

    public function __construct($table_name)
    {
        $this->table_name = $table_name;
        $host = Config::DB_HOST();
        $username = Config::DB_USERNAME();
        $password = Config::DB_PASSWORD();
        $schema = Config::DB_SCHEME();
        $port = Config::DB_PORT();

        /* options array neccessary to enable ssl mode - do not change */
        $options = array(
            PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../../isrgrootx1.pem',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        );

        $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$schema", $username, $password, $options);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /*
     * Method for fetching all entities from the database
     */
    public function get_all()
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Method for fetching a specific entity from the database
     */

    public function get_by_id($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table_name WHERE id=:id");
        $stmt->execute(['id' => $id]); //prevents an SQL injection **binding the parameter
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Method for deleting an entity from the database
     */
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table_name WHERE id = :id");
        $stmt->bindParam(":id", $id); //prevents an SQL injection
        $stmt->execute();
    }

    /*
     * Method for adding a new user or any other entity to the database
     */
    public function add($entity)
    {
        $query = "INSERT INTO $this->table_name (";
        foreach ($entity as $key => $value) {
            $query .= $key . ", ";
        }
        $query = substr($query, 0, -2);
        $query .= ") VALUES (";
        foreach ($entity as $key => $value) {
            $query .= ":" . $key . ", ";
        }
        $query = substr($query, 0, -2);
        $query .= ")";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($entity); //binding to prevent injections

        $entity['id'] = $this->conn->lastInsertId();
        return $entity;
    }

    /*
     * Method for updating an existing user in the database
     */
    public function update($entity, $id, $id_column = 'id')
    {
        $query = "UPDATE $this->table_name SET ";
        foreach ($entity as $key => $value) {
            $query .= $key . "=:" . $key . ", ";
        }
        $query = substr($query, 0, -2);
        $query .= " WHERE $id_column =:id;";

        $stmt = $this->conn->prepare($query);
        $entity['id'] = $id;
        $stmt->execute($entity);
        return $entity;
    }

    public function get_connection(){
        return $this->conn;
    }

    //$stmt->execute(['id'=>$id,'first_name'=>$first_name,'last_name'=>$last_name,'age'=>$age]);

}
