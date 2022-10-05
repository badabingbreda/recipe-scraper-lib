<?php

namespace RecipeRemix\Helpers\WordPress;

class Taxonomy {

    public function __construct() {

        add_action( 'init' , __CLASS__ . '::register_taxes' , 20  );

    }

    public static function register_taxes() {

        self::register( 'recipe_category' , 'Recipe Category' , 'Recipe Categories' );
        self::register( 'meal_type' , 'Meal Type' , 'Meal Types' );
        self::register( 'diet' , 'Diet' , 'Diets' );
        self::register( 'health' , 'Health' , 'Health' );
        self::register( 'cuisinetype' , 'Cuisine' , 'Cuisines' );
        self::register( 'dishtype' , 'Dish' , 'Dishes' );


    }


    /**
     * register_tax
     * 
     * register a taxonomy
     *
     * @param  mixed $slug
     * @param  mixed $singular
     * @param  mixed $plural
     * @return void
     */
    private static function register( $slug , $singular , $plural ) {

        // Add new taxonomy, NOT hierarchical (like tags)
        $labels = array(
            'name'                       => $plural,
            'singular_name'              => $singular,
            'search_items'               => "Search {$plural}",
            'popular_items'              => "Popular {$plural}",
            'all_items'                  => "All {$plural}",
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => "Edit {$singular}",
            'update_item'                => "Update {$singular}",
            'add_new_item'               => "Add New {$singular}",
            'new_item_name'              => "New {$singular}",
            'separate_items_with_commas' => "Separate {$plural} with commas",
            'add_or_remove_items'        => "Add or remove {$plural}",
            'choose_from_most_used'      => "Choose from the most used {$plural}",
            'not_found'                  => "No {$plural} found.",
            'menu_name'                  => "{$plural}",
        );
    
        $args = array(
            'hierarchical'          => false,
            'label'                 => $plural,
            'labels'                => $labels,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'show_tagcloud'         => true,
            'show_in_quick_edit'    => true,
            'show_admin_column'     => true,
            'show_in_rest'          => true,
            'hierarchical'          => false,
            'query_var'             => true,
            'sort'                  => false,
            'rewrite_no_front'      => false,
            'rewrite_hierarchical'  => false,
            'rewrite'               => array( 'slug' => $slug ),
            //'meta_box_cb'           => false,                       // used to DISABLE meta box for tax
            'update_count_callback' => '_update_post_term_count',
        ); 
        
        register_taxonomy( $slug , 'recipe', $args );        
    }    
}