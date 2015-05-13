<?php
/**
 * CLI command to change the table prefix in the database and wp-config file
 */

class Table_Prefix extends WP_CLI_Command {

    /**
     * Changes the prefix of tables in the DB using default database connection
     *
     * ## Options
     *
     * <new>
     * : The new prefix for tables
     *
     * ## EXAMPLE
     *
     *     wp table-prefix change notwp_
     *
     * @synopsis <new> [--old-prefix=<old>] [--custom-table-name-regex=<regex>] [--multisite-prefix=<multi>]
     */
    public function change( $args, $assoc_args ) {
        global $wpdb;
    
        list($new) = $args;

        // from the wp-config.php file ($table_prefix)
        $default_prefix = $wpdb->prefix;
        
        // pull from args, if not in args use default
        $old = (!empty($assoc_args['old']) ? $assoc_args['old'] : $default_prefix);

        $multi = (!empty($assoc_args['multi']) ? $assoc_args['multi'] : '');

        // regex used to verify table prefix
        $default_regex = '/^[A-Za-z][A-Za-z0-9_]+$/';
        
        $regex = (!empty($assoc_args['regex']) ? $assoc_args['regex'] : $default_prefix);
        
        if (!preg_match($regex, $new)) {
            WP_CLI::error('New prefix is invalid', true);
        }

        $tables = self::fetch_tables($old.$multi);

        if (!count($tables)) {
            WP_CLI:error('No tables with specified prefix found' . (!empty($multi) ? ' (using multisite prefix)' : ''), true);
        }

        $new_tables = array();
        foreach ($tables as $table) {
            $new_tables[$table] = str_replace($old, $new, $table);
        }
        
        $res = self::do_rename($new_tables);

        WP_CLI::success('Table prefix changed from "' . $old . '" to "' . $new . '"' . (!empty($multi) ? ' with multisite prefix "' . $multi . '"' : ''));
    }

    /**
     * Displays the current table prefix
     *
     * ## EXAMPLE
     *
     *     wp table-prefix show
     *
     */
    public function show( $args, $assoc_args ) {
        global $wpdb;

        WP_CLI::success('The current table prefix is "' . $wpdb->prefix . '"')
    }

    protected static function fetch_tables($prefix) {
        global $wpdb;
        
        return $wpdb->query($wpdb->prepare("SHOW TABLES FROM " . DB_NAME . " LIKE '" . $prefix . "%'"))
    }
    
    protected static function do_rename($tables) {
        global $wpdb;
    
        $qry = "RENAME TABLE ";
        
        $first = true;
        
        foreach ( $tables as $old => $new ) {
            if (!$first) {
                $qry .= ', ';
            }
            
            $qry .= '`' . $old . '` TO `' . $new . '`';
            
            $first = false;
        }
        
        return $wpdb->query($qry);
    }
}

WP_CLI::add_command( 'table-prefix', 'Table_Prefix' );