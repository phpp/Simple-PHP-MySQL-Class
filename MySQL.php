<?php

/**
 * PHP-MySQL-Class provides basic PHP methods in order to manipulate MySQL database
 * @license     GPL v2
 * @author      Walid Awad <walid@php-programmierer.at>
 * @copyright   Copyright (c) 2014
 * @version     0.1
 * @tutorial    http://www.php-programmierer.at/en/php-and-mysql/php-mysql-class-tutorial
 */
class MySQL {
 
    private $conn = null;
    private $DBHOST = 'localhost'; // Database host
    private $DBUSR = 'root'; // Database user
    private $DBPWD = 'password'; // Database password
    private $DBName = 'databasename'; // Database name


    /**
     * Constructer
     */
    public function __construct() {
        if ($this->conn = mysql_connect($this->DBHOST, $this->DBUSR, $this->DBPWD)) {
            if (!mysql_select_db($this->DBName, $this->conn)) {
                echo 'No database was found';
            }
        } else {
            echo 'Can\'t connect to MySQL server';
        }
    }
    
    /**
     * Creates a table
     * @parm string $tableName
     * @parm array $fields
     * @parm mixed $primaryKey
     */
    public function createTable($tableName, $fields, $primaryKey = false){
        if(($tableName) && (count($fields)) > 0){
            $query = 'CREATE TABLE IF NOT EXISTS `'.$tableName.'`(';
            foreach ($fields as $key => $value) {
                $query .= '`'.$key.'` '.$value.', ';
            }
            
            if($primaryKey) $query .='PRIMARY KEY (`'.$primaryKey.'`)';
            else $query = substr ($query, 0, -2);
            $query .= ');';
            
            $this->res($query);
        }
    }
    
    /**
     * Extends a table
     * @param string $tableName
     * @param array $fields
     */
    public function alterTable($tableName, $fields){
         $query = ' alter table '.$tableName;
         foreach ($fields as $key => $value) {
                $query .= ' add `'.$key.'` '.$value.', ';
            }
            $query = substr ($query, 0, -2);
            $this->res($query);
    }
    
    /**
     * Empties a table 
     * @param string $tableName
     */
    public function truncateTable($tableName){
        $query = 'truncate table '.$tableName;
        $this->res($query);
    }
       
    
    /**
     * Deletes a table
     * @param string $tableName
     */
    public function dropTable($tableName){
        $query = 'drop table '.$tableName;
        $this->res($query);
    }
    
    /**
     * Sends a MySQL query
     * @parm string $query
     */
    public function res($query) {
        mb_internal_encoding('utf8');
        mysql_query("SET NAMES utf8");
        return mysql_query($query, $this->conn);
    }

    /**
     * Gets the number of rows
     * @parm mysql result $res
     * @return integer
     */
    public function num($res) {
        return mysql_num_rows($res);
    }

    /**
     * Fetchs a result row as an associative array,
     * a numeric array, or both
     * @parm mysql result $res
     * @return associative array
     */
    public function row($res) {
        return mysql_fetch_assoc($res);
    }

    /**
     * Gets the last ID generated in query
     * @return integer 
     */
    public function LastInsertID() {
        return mysql_insert_id();
    }

    /**
     * Escapes special characters in a string
     * @parm string|array $fields
     * @return string|array $fields
     */
    public function realScapeString($fields) {
        if (is_array($fields)) {
            foreach ($fields as $key => $val) {
                if (!is_array($fields[$key])) {
                    $fields[$key] = mysql_real_escape_string($fields[$key]);
                }
            }
        } else {
            $fields = mysql_real_escape_string($fields);
        }
        return $fields;
    }

    /**
     * Set where statement
     * @parm array|string $where
     * @return string
     */
    public function setWhere($where) {
        if (is_array($where)) {
            if (count($where) > 0) {
                $ret = ' where ';
                foreach ($where as $key => $val) {
                   $ret .= $key . '= \'' . $val . '\' and ';
                }
                return substr($ret, 0, -5);
            } else {
                return false;
            }
        } else {   
            return ' where '.$where;
        }
    }

    /**
     * Inserts fields into table
     * @parm string $tableName
     * @parm string|array $data
     * @return integer $id
     */
    public function insert($tableName, $data) {

        $sql = 'insert into '.$tableName;
        $columns = '(';
        $values = ' values(';

        $fields = $this->realScapeString($data);
        foreach ($fields as $field => $value) {
            $columns .='`' . $field . '`,';
            $values .='\'' . $this->realScapeString($value) . '\',';          
        }
        
        $columns = substr($columns, 0, -1);
        $values = substr($values, 0, -1);
        $columns .= ')';
        $values .=')';

        $sql .= $columns . $values;
        
        $this->res($sql);
        return $this->LastInsertID();
    }

    /**
     * Updates table fields
     * @parm string $table
     * @parm array $data
     * @parm array $where
     */
    public function update($tableName, $data, $where) {

        $query = 'update '.$tableName . ' ';
        $set = 'set ';
        $fields = $this->realScapeString($data);
        
        foreach ($fields as $key => $value) {
            $set .= '`' . $key . '`=\'' . $value . '\',';
        }

        $query .= substr($set, 0, -1);
        $query .= $this->setWhere($where);
        $this->res($query);

    }

    /**
     * Deletes rows from table 
     * @parm string $table
     * @parm array $where
     */
    public function delete($tableName, $where) {
        $query  = 'delete from '.$tableName;
        $query .= $this->setWhere($where);
        $this->res($query);
    }

    /**
     * Close MySQL
     */
    public function close() {
        mysql_close($this->conn);
    }

}

?>
