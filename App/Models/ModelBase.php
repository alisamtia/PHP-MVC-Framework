<?php

namespace App\Models;

use Core\Database;

class ModelBase
{
    protected static $db;

    protected static function db()
    {
        if (!static::$db) {
            static::$db = new Database();
        }
        return static::$db;
    }

    protected static function verify_data($data_keys, $parent_set = null, $error_txt = null)
    {
        if (!$parent_set) {
            $parent_set = static::COLUMNS;
        }
        if (!array_subset($parent_set, $data_keys)) {
            if (!$error_txt) {
                $current_class_name = static::class;
                $error_txt = "The array in model '$current_class_name' does not match the array given in.\n Make sure columns in both arrays are case sensitive in lower-case.";
            }
            throw new \Exception($error_txt);
        }
    }

    protected static function verify_limit($offset, $limit)
    {
        if ($offset !== 0) {
            if (!is_int($offset)) {
                throw new \Exception("Offset is not type of a int!");
            }
        }
        if ($limit !== 0) {
            if (!is_int($limit)) {
                throw new \Exception("Limit is not type of a int!");
            }
        }
    }

    protected static function where_initializer(array $data, $str_condition = "", $extra_table_name = "")
    {
        if (empty($data) && $str_condition == "") {
            return "";
        }
        if (empty($str_condition)) {
            $array_keys = array_keys($data);
            self::verify_data($array_keys);
            $result = [];
            foreach ($array_keys as $key) {
                if ($extra_table_name == "") {
                    $result[] = "$key=:$key";
                } else {
                    $result[] = "$extra_table_name.$key=:$key";
                }
            }
            $condition = implode(" AND ", $result);
        } else {
            $condition = $str_condition;
        }
        return "WHERE $condition";
    }

    protected static function where($data, $str_condition = "", $limit = 0, $offset = 0)
    {
        $condition = self::where_initializer($data, $str_condition);
        $table = static::TABLE;
        self::verify_limit($offset, $limit);
        $condition = $condition . ($limit ? " LIMIT $limit" : "") . ($offset ? " OFFSET $offset" : "");
        return static::db()->query(
            "SELECT * FROM $table $condition",
            $data
        );
    }

    protected static function count_where($keys)
    {
        $condition = self::where_initializer($keys);
        $table = static::TABLE;
        return static::db()->query(
            "SELECT COUNT(*) AS count FROM $table $condition",
            $keys
        )->fetch()['count'];
    }

    protected static function all()
    {
        $table = static::TABLE;
        return static::db()->query(
            "SELECT * FROM $table",
        );
    }

    protected static function destroy($data, $str_condition = "")
    {
        $condition = self::where_initializer($data, $str_condition);
        $table = static::TABLE;
        return static::db()->query(
            "DELETE FROM $table $condition",
            $data
        );
    }

    protected static function store(array $array)
    {
        $array_keys = array_keys($array);
        self::verify_data($array_keys);
        $column_names = implode(",", $array_keys);
        $wildcards = ":" . implode(",:", $array_keys);
        $table = static::TABLE;

        static::db()->query(
            "INSERT INTO $table ($column_names) VALUES ($wildcards)",
            $array
        );
    }

    protected static function update(array $conditions, array $array)
    {
        $condition_keys = array_keys($conditions);
        self::verify_data($condition_keys);
        $array_keys = array_keys($array);

        $wildcard_conditions = array_map(
            fn($key) => "$key=:$key",
            $condition_keys
        );
        $result = array_map(
            fn($key) => "$key=:$key",
            $array_keys
        );
        $wildcards_update = implode(",", $result);
        $wildcard_conditions = implode(" AND ", $wildcard_conditions);

        $table = static::TABLE;
        static::db()->query(
            "UPDATE $table SET $wildcards_update WHERE $wildcard_conditions;",
            array_merge($conditions, $array)
        );
    }

    protected static function preg_match_if($searched_ref_val)
    {
        return !($searched_ref_val === 0 ? $searched_ref_val + 1 : $searched_ref_val);
    }



    /**
     * Pass the name of the refrance in $conditions
     * Pass the current model columns as the keys in $condition
     * Pass the refrance model columns as the values in $conditions
     */
    protected static function join_initializer(array $conditions)
    {
        $current_model = static::class;
        $current_model_table = $current_model::TABLE;
        $refrance_columns_names = [];
        $join_columns = [];
        $this_verify = true;
        $refrances_used = [];

        foreach ($conditions as $key => $val) {
            // Determine which side has 'this' and which has the reference
            if (preg_match('/this.(?P<col>\w+)/', $key, $matches_this) && preg_match('/(?P<ref>\w+).(?P<col>\w+)/', $val, $matches_ref)) {
                $this_side = $key;
                $ref_side  = $val;
            } elseif (preg_match('/this.(?P<col>\w+)/', $val, $matches_this) && preg_match('/(?P<ref>\w+).(?P<col>\w+)/', $key, $matches_ref)) {
                $this_side = $val;
                $ref_side  = $key;
            } elseif (preg_match('/(?P<ref>\w+).(?P<col>\w+)/', $val, $matches_this) && preg_match('/(?P<ref>\w+).(?P<col>\w+)/', $key, $matches_ref)) {
                // $this_side = $key;
                if (in_array($matches_this['ref'], $refrances_used)) {
                    $this_side = $val;
                } elseif (in_array($matches_ref['ref'], $refrances_used)) {
                    $this_side = $key;
                    $key = $val;

                    $temp_matches_this = $matches_this;
                    $matches_this = $matches_ref;
                    $matches_ref = $temp_matches_this;
                } else {
                    throw new \Exception("Refrance was not already used $current_model");
                }
                $this_verify = false;
            } else {
                throw new \Exception("Incorrect refrance in the model $current_model");
            }

            // verify if the refrance class exists and refrance exists in the current model
            $ref_class_name = $matches_ref['ref'];
            if (!isset(static::REFRENCES[$ref_class_name])) {
                throw new \Exception("The '$ref_class_name' refrance does not exists in model '$current_model'");
            }

            // check if $name refrance class exists
            $refrance_model = static::REFRENCES[$ref_class_name];
            if (!class_exists($refrance_model)) {
                $class_name = static::REFRENCES[$ref_class_name];
                throw new \Exception("The '$class_name' does not exists at model '$current_model'");
            }

            // verify $matches_this['col'] exists in current model's columns
            if ($this_verify) {
                $searched_ref_val = array_search($matches_this['col'], static::COLUMNS);
                if (self::preg_match_if($searched_ref_val)) {
                    $col = $matches_this['col'];
                    throw new \Exception("this.$col does not exists in the current model");
                }
            }

            // verify $matches_ref['col'] exists in reference model's columns
            $refrance_model_columns = $refrance_model::COLUMNS;
            $searched_ref_val = array_search($matches_ref['col'], $refrance_model_columns);
            if (self::preg_match_if($searched_ref_val)) {
                throw new \Exception("$key does not exists in the class $refrance_model");
            }

            $ref_table = $refrance_model::TABLE;
            $ref = $matches_ref['ref'];
            $refrance_columns_new = array_map(
                fn($col) => "$ref.$col as {$ref}_{$col}",
                $refrance_model::COLUMNS
            );
            $refrance_columns_names = array_merge($refrance_columns_names, $refrance_columns_new);

            $col_1 = str_replace("this", static::TABLE, $this_side);
            $col_2 = $ref . '.' . $matches_ref['col'];
            $join_columns[] = "JOIN $ref_table as $ref ON $col_1 = $col_2";
            $refrances_used[] = $ref;
        }
        $join_columns = implode(' ', $join_columns);
        $refrance_columns_names = implode(',', $refrance_columns_names);
        $sql = "SELECT $current_model_table.*, $refrance_columns_names FROM $current_model_table $join_columns";
        return $sql;
    }


    protected static function join(array $join, $limit = 0, $offset = 0)
    {
        $join = self::join_initializer($join);
        self::verify_limit($offset, $limit);
        $query = $join . ($limit ? " LIMIT $limit" : "") . ($offset ? " OFFSET $offset" : "");
        return static::db()->query($query);
    }

    protected static function join_where(array $join, $where_conditions, $str_where = "", $limit = 0, $offset = 0)
    {
        // make join_where more flexible and add the allowance to do conditioning based on the current refrances after verifying
        $current_model_table = static::TABLE;
        $where_query = self::where_initializer($where_conditions, $str_where, $current_model_table);
        $query = self::join_initializer($join) . " " . $where_query;
        self::verify_limit($offset, $limit);
        $query = $query . ($limit ? " LIMIT $limit" : "") . ($offset ? " OFFSET $offset" : "");
        // dd($query);
        return static::db()->query($query, $where_conditions);
    }
}
