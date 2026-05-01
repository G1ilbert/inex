<?php

namespace Database;

use Exception;

class ViewManager
{

    public static function syncAll($log = null): void
    {
        $viewNamespace = 'Database\\Views\\';
        $views = [];

        // load views
        foreach (glob('php/Database/Views/*.php') as $file) {
            require_once $file;
            $className = $viewNamespace . pathinfo($file, PATHINFO_FILENAME);
            if (!class_exists($className)) {
                echo "Class $className not found.\n";
                continue;
            }
            $view = new $className();
            if (!property_exists($view, 'name') || !property_exists($view, 'select') || !property_exists($view, 'requires')) {
                echo "Invalid view class: $className\n";
                continue;
            }
            $views[$view->name] = $view;
        }

        // topological sort helper function
        $sorted = [];
        $visited = [];
        $temp = [];

        $visit = function($name) use (&$visit, &$views, &$sorted, &$visited, &$temp) {
            if (isset($visited[$name])) return;
            if (isset($temp[$name])) {
                throw new Exception("Circular dependency detected at view: $name");
            }
            $temp[$name] = true;
            if (!isset($views[$name])) {
                throw new Exception("Missing view dependency: $name");
            }
            foreach ($views[$name]->requires as $dep) {
                $visit($dep);
            }
            $visited[$name] = true;
            $sorted[] = $name;
        };

        // run sort on all views
        foreach (array_keys($views) as $name) {
            $visit($name);
        }

        $syncedViews = [];

        // sync views in sorted order
        foreach ($sorted as $viewName) {
            $view = $views[$viewName];
            $fullViewName = "DYN_" . $view->name;
            $keepGoing = false;
            for ($x = 0; $x < 8; $x++) {
                if ($x > 0 && $keepGoing) break;
                $keepGoing = false;
                try {
                    self::syncView($fullViewName, $view->select, $log);
                    $syncedViews[] = $fullViewName;
                } catch (Exception $e) {
                    echo $e;
                    $keepGoing = true;
                }
            }
        }
    }


    public static function syncView(string $name, string $selectSql, $log = null): void
    {
        $normalizedNewSql = preg_replace('/\s+/', ' ', trim($selectSql));

        $result = Connection::execSelect(
            "SELECT VIEW_DEFINITION FROM information_schema.VIEWS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()",
            "s",
            [$name]
        );

        $currentDef = $result[0]["VIEW_DEFINITION"] ?? '';
        $normalizedCurrentSql = preg_replace('/\s+/', ' ', trim($currentDef));

        if ($normalizedNewSql !== $normalizedCurrentSql) {
            if ($log) $log("Updating view: $name");
            else echo "Updating view: $name\n";
            Connection::execSimpleOperation("CREATE OR REPLACE VIEW `$name` AS $selectSql");
        } else {
            if ($log) $log("View $name is up to date.");
            else echo "View $name is up to date.\n";
        }
    }
}