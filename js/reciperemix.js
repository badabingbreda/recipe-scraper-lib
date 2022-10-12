recipeRemix = function( settings ) {

    this.hookPrefix = `reciperemix`;

}

recipeRemix.prototype = {

    getRecipe: function( url ) {

        $this = this;
        jQuery.ajax( {
            type: 'POST',
            url: `/wp-admin/admin-ajax.php?action=scrape_recipe&url=${url}`,
            data: {},
            success: (data) => {
                
                data = JSON.parse( data );
                console.log( data );

                $this.triggerHook( 'success' , [ { ...data } ] );
                
            }
        });        
    },

   /**
     * Trigger a hook.
     *
     * @since 1.0
     * @method triggerHook
     * @param {String} hook The hook to trigger.
     * @param {Array} args An array of args to pass to the hook.
     */
    triggerHook: function( hook, args ) {
        jQuery( 'body' ).trigger( `${this.hookPrefix}.` + hook, args );
    },

	/**
	 * Add a hook.
	 *
	 * @since 1.0
	 * @method addHook
	 * @param {String} hook The hook to add.
	 * @param {Function} callback A function to call when the hook is triggered.
	 */
    addHook: function( hook, callback ) {
        jQuery( 'body' ).on( `${this.hookPrefix}.` + hook, callback );
    },    

    /**
     * Remove a hook.
     *
     * @since 1.0
     * @method addHook
     * @param {String} hook The hook to remove.
     * @param {Function} callback better to provide callback name/pointer.
     */
    deleteHook: function( hook, callback ) {
        jQuery( 'body' ).off( `${this.hookPrefix}.` + hook, callback );
    },     
};

(function($){

    $(document).ready( function() {

        const reciperemix = new recipeRemix();

        const getrecipe = function( e ) {

            const url = $( '#recipeurl' ).val();
            reciperemix.getRecipe( url );

        };

        $( '.recipe-submit' ).on( 'click' , getrecipe );

        reciperemix.addHook( 'success' , function( event, data ) { 
            if ( data.status.code == 200 ) {
                $( '#messagearea' ).text( 'Success! Forwarding you to recipe now!' );
                window.setTimeout( function(){ window.location = data.recipeLink; }.bind( data ) , 1000 );
                //window.location = data.recipeLink;
            } else {
                $( '#messagearea' ).text( data.status.message );
                //alert( data.status.message ); 
            }
        } );


    });

})(jQuery);
