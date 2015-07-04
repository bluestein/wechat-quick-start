<?php

/**
 * File: db.class.php
 * Created by humooo.
 * Email: humooo@outlook.com
 * Date: 15-6-23
 * Time: 下午5:01
 */
include_once '../conf.global.php';
include_once '../conf.local.php';

class Db
{
    private $db;
    public $level = 'error';
    public $error = null;

    public function __construct()
    {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PWD, DB, DB_PORT);
        $sql = & $this->db;
        if ($sql->connect_errno) {
            $this->error = "Failed to connect to MySQL: (" . $sql->connect_errno . ") " . $sql->connect_error;
            return;
        }
        $sql->set_charset("utf8");
        $sql->autocommit(false);
    }

    public function insert($tb_name = null, $data = null)
    {
        $sql = & $this->db;
        $values = '';
        if ($tb_name && $data) {
            if (is_array($data)) {
                foreach ($data as $item) {
                    if (is_numeric($item)) {
                        $values .= "," . $item;
                    }
                    $values .= ",'" . $item . "'";
                }
            } else {
                if (is_numeric($data)) {
                    $values .= "," . $data;
                }
                $values .= ",'" . $data . "'";
            }
            $query = "INSERT INTO " . $tb_name . " VALUES (" . ltrim($values, ',') . ")";
            if (DEBUG) echo $query;
            try {
                if (!$sql->query($query)) throw new Exception('error');
            } catch (Exception $e) {
                $this->error = "Failed to query: (" . $sql->errno . ") " . $sql->error;
                $this->error .= " at: file-class-function-line( " . basename(__FILE__) . "-" . __CLASS__ . "-" . __FUNCTION__ . "-" . __LINE__ . ")";
                return false;
            }
            if (!$sql->commit()) {
                $sql->rollback();
                $this->error .= "Transaction commit failed ";
                return false;
            }
            return true;
        }
        return false;
    }

    /*
     $data = array(
            'select' => '*',
            'where' => array(
                       'state' => 1,
                       'order' => 1
                       ),
            'table' => TB_NEWS,
            'order' => 'Time',
            'limit' => 10
        );
     */
    public function fetch($sql_query = null)
    {
        if ($sql_query && is_array($sql_query)) {

            $sql = & $this->db;

            $where = null;
            if (isset($sql_query['where']) && is_array($sql_query['where'])) {
                foreach ($sql_query['where'] as $name => $value) {
                    $where .= " " . $name . "=" . "'" . $value . "' AND ";
                }
            }
            if (isset($sql_query['like']) && is_array($sql_query['like'])) {
                foreach ($sql_query['like'] as $name => $value) {
                    $where .= " " . $name . " LIKE " . "'" . $value . "' AND ";
                }
            }
            if ($where)
                $where = " WHERE " . rtrim($where, "AND ");

            $query = "SELECT " . $sql_query['select'];
            $query .= " FROM " . $sql_query['table'];
            $query .= $where;
            $query .= isset($sql_query['order']) ? " ORDER BY " . $sql_query['order'] : "";
            $query .= isset($sql_query['limit']) ? " LIMIT " . $sql_query['limit'] : "";
            if (DEBUG) echo $query;
            try {
                if (!$result = $sql->query($query)) throw new Exception('error');
            } catch (Exception $e) {
                $this->error = "Failed to query: (" . $sql->errno . ") " . $sql->error;
                $this->error .= " at: file-class-function-line( " . basename(__FILE__) . "-" . __CLASS__ . "-" . __FUNCTION__ . "-" . __LINE__ . ")";
                return false;
            }
            if (!$sql->commit()) {
                $sql->rollback();
                $this->error .= "Transaction commit failed ";
                return false;
            }
            $return = array();
            while ($row = $result->fetch_assoc())
                $return[] = $row;
            $result->free();
            return $return;
        }
        return false;
    }


    public function __destruct()
    {
        $this->db->close();
    }
}