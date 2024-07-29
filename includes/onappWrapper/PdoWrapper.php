<?php

/* * ********************************************************************
 * ModulesGarden product developed. (2016-11-11)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->       http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 * ******************************************************************** */
namespace onappWrapper;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Description of PdoWrapper
 *
 * @author Mateusz Tomaszewski
 * @version 1.0.0
 */
class PdoWrapper {

    public static function query($query, $params=array()) {
        $statement = DB::connection()
                ->getPdo()
                ->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    public static function realEscapeString($string) {
        return substr(DB::connection()->getPdo()->quote($string), 1, -1);
    }

    public static function fetchAssoc($query) {
        return $query->fetch(\PDO::FETCH_ASSOC);
    }

    public static function fetchArray($query) {
        $data = array();
        while($row= $query->fetch(\PDO::FETCH_ASSOC)){
            $data[]= $row;
        }
        return $data;
    }

    public static function fetchObject($query) {
        return $query->fetch(\PDO::FETCH_OBJ);
    }

    public static function numRows($query) {
        $query->fetch(\PDO::FETCH_BOTH);
        return $query->rowCount();
    }

    public static function insertId() {
        return DB::connection()
                        ->getPdo()
                        ->lastInsertId();
    }

}