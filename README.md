# table-prefix
WP CLI command for changing table prefixes. In addition to changing the table prefix the wp-config file will also be updated. A back of the wp-config file is saved to wp-config.bak.php in case something go awry.

##Commands

change <prefix>
   changed the prefix of the tables in the database to <prefix>, unless specified otherwise the default prefix will be used (see: show)

show
   shows the current prefix the database object thinks it is using ($wpdb->prefix)

##Example Usage

wp table-prefix change notwp_

##Future Enhancment Ideas

* Enhanced multisite support, currently only changes main table prefix and doesn't allow you to change the site prefix (ex. wp_2_site can change to x_2_site but not wp_x_site)
* Moar docs 


