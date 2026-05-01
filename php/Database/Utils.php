<?php

namespace Database;

class Utils
{
    static function GetCols($tableName)
    {
        $schema_columns = Connection::execSelect("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME = ?
        AND TABLE_SCHEMA = ?
    ", "ss", [$tableName, DATABASE_NAME]);

        $columns = [];
        foreach ($schema_columns as $col) {
            $columns[] = $col['COLUMN_NAME'];
        }

        return $columns;
    }

    public static function GenerateJSONObject(string $table, array $cols, array $exclude = [])
    {
        $jsonFields = [];

        foreach ($cols as $col) {
            if (in_array($col, $exclude, true)) {
                continue; // skip excluded columns
            }
            // key is the column name as string, value is fully qualified: table.col
            $jsonFields[] = "'$col', {$table}.`$col`";
        }

        echo "\n";
        echo json_encode($jsonFields, JSON_PRETTY_PRINT);
        echo "\n";
        return implode(", \n", $jsonFields);
    }

}