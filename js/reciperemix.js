recipeRemix = function ( settings ) {
    this.settings = settings ?? {};
    this.debounceTimeout = 250;
    this.updateContainer = this.settings?.updateContainer ?? '.recipe-target';
    this.editClass = this.settings?.editClass ?? 'remix-editable';
    this.whileEditingClass = this.settings?.whileEditingClass ?? 'remix-editing';
    this.sortableHandle = '<span class="remix-handle-sort uk-sortable-handle uk-margin-small-right uk-text-center" uk-icon="icon: table"></span>';
    this.editHandle = '<span class="remix-handle-edit uk-margin-small-right uk-text-center" uk-icon="icon: pencil"></span>';
    this.trashHandle = '<span class="uk-margin-small-right uk-text-center" uk-icon="icon: trash"></span>';
    this.checkHandle = '<span class="uk-margin-small-right uk-text-center" uk-icon="icon: check"></span>';

    this._init();
}

recipeRemix.prototype = {

    _init: function() {

        // store this so we can use it in jQuery.each()
        const $this = this;

        jQuery( this.updateContainer ).addClass( 'reciperemix' );

        // do some stuff to init the list editable areas
        this.initListEditable();
        this.initSingleEditable();

        jQuery( 'body' ).on( 'click' , '.remix-add-option' , function( e ) {
            $elem = jQuery(e.target);
            let controls = $elem.attr( 'data-controls' );
            console.log(  controls );
            this.addOption( `${this.updateContainer} ul[class*="${controls}"]` );
        }.bind( this ) );

        jQuery( 'body' ).on( 'click' , '[remix-option-save]' , function( e ) {

            const $this = this;
            let $elem = jQuery( e.target );
            let listElem = $elem.closest( 'ul' );
            let control = $elem.closest( `[class*="remix-unsaved"]` );
            // find the controller element
            // store content of textarea
            let contents = control.find( 'textarea' ).val();
            control.replaceWith( `<li>${contents}</li>` );
            listElem.find( 'li:not( [class*="remix-item"] )' ).each( function( index) { $this.remixItem( this , true ); } );

        }.bind( this ) );

        jQuery( 'body' ).on( 'click' , '[remix-option-trash]' , function( e ) {
            const $this = this;
            let $elem = jQuery( e.target );
            let control = $elem.closest( `[class*="remix-unsaved"]` );
            control.remove();
        }.bind( this ) );


    },

    initListEditable: function() {

        // store this so we can use it in jQuery.each()
        const $this = this;
        
        // make sure they are dragable
        
        $editable_lists = jQuery( `${this.updateContainer} ul[${this.editClass}]` );

        $editable_lists.each( function(index){

            jQuery(this).attr( 'uk-sortable' , 'handle: li; cls-drag: uk-sortable-drag remix-drag;' );
            jQuery(this).addClass( `remixsort${index}` );
            // add a button that we can use to insert new items to the list
            jQuery(this).after( `<button class="remix-add-option" data-controls="remixsort${index}">Add</button>` );
        });

        // make all li items into sortable items
        jQuery( `${this.updateContainer} [${this.editClass}] li` ).each( function( index ){ $this.remixItem( this , true ); });

    },

    initSingleEditable: function() {

        const $this = this;

        jQuery( `${this.updateContainer} [${this.editClass}]:not(ul,li)` ).each( function( index ){ $this.remixItem( this , false ); } );

    },

    remixItem( item , addSort ) {
        jQuery( item ).addClass( 'remix-item' );
        // wrap the editable area so we can target
        jQuery( item ).wrapInner( '<span editable-area/>' );
        // add icons for control
        if (addSort || false ) jQuery( item ).prepend( this.sortableHandle );
        jQuery( item ).append( this.editHandle );
    },

    addOption( target ) {

        jQuery( target ).append( `<li class="uk-position-relative remix-unsaved"><textarea></textarea><div style="position:absolute;top:0;left:-24px;" remix-option-save>${this.checkHandle}</div><div style="position:absolute;top:24px;left:-24px;" remix-option-trash>${this.trashHandle}</div></li>` );

    },
    
    getRecipe: function( ) {
        alert( 'yes' );

        const url = jQuery( 'input[name="recipeurl"]' ).val();

        jQuery.ajax( {
            type: 'POST',
            url: `/wp-admin/admin-ajax.php?action=scrape_recipe&url=${url}`,
            data: {},
            success: (data) => {
                
                data = JSON.parse( data );
                alert( data.status.message );
                
            }
        });        
    },

    remixToggle: function() {

        const $this = this;

        if ( !jQuery( this.updateContainer ).hasClass( this.whileEditingClass ) ) {

            jQuery( `${this.updateContainer} [${this.editClass}] li [editable-area], ${this.updateContainer} [${this.editClass}]:not(ul,li) [editable-area]` ).each( function( index ) { jQuery(this).prop( 'contentEditable' , true ); } );
            jQuery( this.updateContainer ).addClass( this.whileEditingClass );
            jQuery( `${this.updateContainer} .uk-sortable` ).each( function(index){ UIkit.sortable( this ).$destroy(); } );
        } else {

            jQuery( `${this.updateContainer} [${this.editClass}] li [editable-area], ${this.updateContainer} [${this.editClass}]:not(ul,li) [editable-area]` ).each( function( index ) { jQuery(this).prop( 'contentEditable' , false ); } );
            jQuery( this.updateContainer ).removeClass( this.whileEditingClass );
            // 
            UIkit.sortable( `${this.updateContainer} .uk-sortable` );
        }        
    },

    _collect: function() {
        const $this = this;
        let collected = {};
        jQuery( `${this.updateContainer} [${this.editClass}]` ).each( function( index ) {
            let $elem = jQuery( this );
            console.log( $elem );
            switch ( $elem[0].tagName ) {
                case "UL":
                    // get all li [editable-area]
                    let options = [];
                    $elem.find( 'li [editable-area]' ).each( function( item ) { options.push( { [ $elem.attr( 'remix-sub-name' ) ] : jQuery( this ).text() } ) } );
                    collected = { ...collected, ...{ [ $elem.attr( 'remix-name' ) ] : options } };
                    break;
                default:
                    collected = { ...collected, ...{ [ $elem.attr( 'remix-name' ) ] : $elem.find('[editable-area]').text() } };
            }
        });

        return collected;
    },

    /**
     * Debounce resize to prevent too many
     */
         _debounce: function( ){	
            clearTimeout( this.debounceTimer );
            this.debounceTimer = setTimeout( function() {
                this.remixToggle();
            }.bind( this ), this.debounceTimeout );
    
        },

};


(function( $ ) {
    $(document).ready( function() {

        const RR = new recipeRemix();

        $('.recipe-submit').on( 'click' , function() {
            RR.getRecipe();
        } );

        $( '.edit-remix' ).on( 'click' , function() {
            RR._debounce();
        } );

        $( '.collect' ).on( 'click' , function() {
            let collection = RR._collect();

            jQuery.ajax( {
                type: 'POST',
                url: `/wp-admin/admin-ajax.php?action=update_recipe`,
                data: { 
                    postid: 45,
                    reciperemix: collection
                },
                success: (data) => {
                    
                    data = JSON.parse( data );
                    console.log( data );
                    alert( data.status.message );
                    
                }
            });

        } );

    });
})(jQuery);

