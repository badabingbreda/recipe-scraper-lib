<?php
namespace RecipeRemix\Integration\ACF;

class LocalJson extends \RecipeRemix\Integration\ACF {

    protected static $groups = array(
        'group_6335ab7236eff',
    );


    public function __construct() {
        // add fitler before acf saves a group
        add_action('acf/update_field_group', __CLASS__ . '::update_field_group' , 1, 1);

        add_filter( 'acf/settings/load_json' , __CLASS__ . '::add_json_location' );
      } 
            
      /**
       * update_field_group
       *
       * @param  mixed $group
       * @return void
       */
      public static function update_field_group($group) {

        // called when ACF save the field group to the DB
        // change the location to store the json file to our plugin path
        if (in_array($group['key'], self::$groups)) {

          // if it is one of my groups then add a filter on the save location
          // high priority to make sure it is not overrridded, I hope
          add_filter('acf/settings/save_json',  __CLASS__ . '::override_location' , 9999);
        }
        return $group;
      }
      
      /**
       * override_location
       *
       * @param  mixed $path
       * @return void
       */
      public static function override_location($path) {
        // remove this filter so it will not effect other groups
        remove_filter('acf/settings/save_json',  __CLASS__ .  '::override_location' , 9999);
        // override save path
        $path = RECIPEREMIX_DIR . 'acf-json';
        error_log( 'changing save path: ' . $path );

        return $path;
      }
      
      /**
       * add_json_location
       *
       * @param  mixed $paths
       * @return void
       */
      public static function add_json_location( $paths ) {
        $paths[] = RECIPEREMIX_DIR . 'acf-json';
        
        return $paths;
      }
}