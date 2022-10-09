/**
 * On Page Editor
 * 
 * jQuery on page editor
 */
onPageEditor = function ( settings ) {
    this.settings           	= settings || {};
    this.targetSelector			= this.settings?.targetSelector || '.ope-target';		// use ope-target
    this.editSelector          	= this.settings?.editSelector || '[ope-editable]';		// treat these areas as editable
    this.whileEditingClass		= this.settings?.whileEditingClass || 'ope-editing';	// class to add/check for to see if editing
    this.uniqueid				= this.settings?.uniqueid || Date.now();				// create a id
    this.debounceTimeout 		= 250;													// debounce timeout so that we can't update too quickly
	this.sortables				= [];
	this.sortablePrefix			= 'ope-sort';

    this.sortableHandle 		= `<span class="ope-handle-sort" uk-icon="icon: table"></span>`;
    this.editHandle 			= `<span class="ope-handle-edit" uk-icon="icon: pencil"></span>`;
    this.trashHandle 			= `<span class="ope-handle-trash" uk-icon="icon: trash"></span>`;
    this.checkHandle 			= `<span class="ope-handle-check" uk-icon="icon: check"></span>`;
    this.moveHandles            = `<div class="ope-handle-move"><div class="ope-handle-up" uk-icon="icon: chevron-up"></div><div class="ope-handle-down" uk-icon="icon: chevron-down"></div></div>`;
	this.addOptionHandle		= ( sortableIdentifier ) => `<button class="ope-add-option" data-controls="${sortableIdentifier}">Add</button>`;
	this.hookPrefix				= `onpageeditor`;
    this._init();
}

onPageEditor.prototype = {

    _init: function() {

        // store this so we can use it in jQuery.each()
        const $this = this;

        jQuery( this.targetSelector ).addClass( 'onpageeditor' );

        // collect all onpageeditor editable areas
        this._createCollection();

        // do some stuff to init the list editable areas
        this.initSingleEditable();
        this.initListEditable();

        
        this._createAddOptions();

        jQuery( 'body' ).on( 'click' , '[ope-option-save]' , function( e ) {

            const $this = this;
            let $elem = jQuery( e.target );
            let listElem = $elem.closest( 'ul' );
            let control = $elem.closest( `[class*="ope-unsaved"]` );
            // find the controller element
            // store content of textarea
            let contents = control.find( 'textarea' ).val();
            control.replaceWith( `<li>${contents}</li>` );
            listElem.find( 'li:not( [class*="ope-item"] )' ).each( function( index) { $this.remixItem( this , true ); } );
            
        }.bind( this ) );
        
        jQuery( 'body' ).on( 'click' , '[ope-option-trash]' , function( e ) {
            const $this = this;
            let $elem = jQuery( e.target );
            let control = $elem.closest( `[class*="ope-unsaved"]` );
            control.remove();
        }.bind( this ) );
        
        $this.triggerHook( 'init-after' [ { ...this } ] );

    },

    /**
	 * create a collection out of the editSelector elements so we have a reference for later
	 */
	_createCollection: function() {
        this.collection = jQuery( this.targetSelector ).find( this.editSelector );
    },
    
    _createAddOptions: function() {

        jQuery( 'body' ).on( 'click' , '.ope-add-option' , function( e ) {
            $elem = jQuery(e.target);
            let controls = $elem.attr( 'data-controls' );
            this.addOption( `${this.updateContainer} ul[class*="${controls}"]` );
        }.bind( this ) );

    },

    _strip: function(html) {
        let doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.body.textContent.trim() || "";        
    },

    initListEditable: function() {

        // store this so we can use it in jQuery.each()
        const $this = this;
        
        this.collection.filter( 'ul' ).each( function(index){
			let sortableIdentifier = `${$this.sortablePrefix}${index}`;
			// enable Sortable for this field
			$this.sortables[ sortableIdentifier ] = Sortable.create( this , { scroll: true , bubbleScroll : true } );
			// add class so we can target when we need to add/remove things
			jQuery(this).addClass( sortableIdentifier );

			// add a button that we can use to insert new items to the list
            jQuery(this).after( $this.addOptionHandle(sortableIdentifier) );

			$this.makeEditable( this );
        });

    },

    initSingleEditable: function() {

        const $this = this;

		// make sure to only ul and li elements with this method
		this.collection.filter( ':not(ul,li)' ).each( function( index ) {
			$this.makeEditable( this );
		});

    },

    reverse: function( index ) {
        let sortableIdentifier = `${$this.sortablePrefix}${index}`;
        var order = this.sortables[sortableIdentifier ].toArray();

        var new_order = this.array_move( order, 0, 1 );
        this.sortables[ sortableIdentifier ].sort(new_order, true);
    },

    move_item: function( sortableindex , itemindex , direction ) {

        let sortableIdentifier = null;

        if ( itemindex == 0 && direction == 'up' ) return;
        if ( typeof sortableindex == 'number' ) {
            sortableIdentifier = `${$this.sortablePrefix}${sortableindex}`;
        } else {
            sortableIdentifier = sortableindex;
        }
        var order = this.sortables[sortableIdentifier ].toArray();
        this.array_move( order , itemindex , direction == 'up' ? itemindex - 1 : itemindex + 1  );
        this.sortables[ sortableIdentifier ].sort(order, true);

    },

    /**
     * move an element in an array
     * @param {array} arr 
     * @param {integer} old_index 
     * @param {integer} new_index 
     * @returns 
     * @url https://stackoverflow.com/a/5306832
     */
    array_move: function(arr, old_index, new_index) {
   
        if (new_index >= arr.length) {
            var k = new_index - arr.length + 1;
            while (k--) {
                arr.push(undefined);
            }
        }
        arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
        return arr; // for testing
    },

    makeEditable( item ) {

		$this = this;

		// perform the hooks that are added
		$this.triggerHook( 'makeeditable' );

		if ( item.tagName == 'UL' ) {
			jQuery( item ).find( 'li' ).each( function( index ) {

				jQuery( this ).addClass( 'remix-item' );
				// wrap the editable area so we can target
				jQuery( this ).wrapInner( '<div editable-area/>' );
				// add icons for control
				jQuery( this ).prepend( $this.sortableHandle );
				jQuery( this ).append( $this.editHandle );
                jQuery( this ).append( $this.moveHandles );
	
			});
		} else {
			
			jQuery( item ).addClass( 'remix-item' );
			// wrap the editable area so we can target
			jQuery( item ).wrapInner( '<div editable-area/>' );
			jQuery( item ).append( $this.editHandle );
			
		}
    },

    addOption( target ) {

        jQuery( target ).append( `<li class="uk-position-relative remix-unsaved"><textarea></textarea><div style="position:absolute;top:0;left:-24px;" remix-option-save>${this.checkHandle}</div><div style="position:absolute;top:24px;left:-24px;" remix-option-trash>${this.trashHandle}</div></li>` );

    },
    
    toggleEdit: function( options ) {

        const $this = this;

        // perform the hooks that are added
		this.triggerHook( 'toggle' );

        if ( options.toggleButton || false ) jQuery( options.toggleButton ).each( function() { 
			jQuery( this ).toggleClass( 'active' ); 
		} );

        if ( !jQuery( this.targetSelector ).hasClass( $this.whileEditingClass ) ) {

            $this.editableStatus( true );
            jQuery( $this.targetSelector ).addClass( $this.whileEditingClass );

        } else {

            $this.editableStatus( false );
            jQuery( $this.targetSelector ).removeClass( $this.whileEditingClass );
        }        
    },

    /**
     * changes the status of the editable areas true/false
     * 
     * @param {boolean} state 
     */
    editableStatus: function( state ) {

        const $this = this;

        $this.collection.find( '[editable-area]' ).each( function( index ) { 
            jQuery(this).prop( 'contentEditable' , state ); 
        } );

    },

    /**
     * collect and return the editable areas keynames and values
     * 
     * @returns object
     */
    collect: function() {
        const $this = this;

		// perform the hooks that are added
		$this.triggerHook( 'collect' );

		let collected = {};
        $this.collection.each( function( index ) {
            let $elem = jQuery( this );
            switch ( $elem[0].tagName ) {
                case "UL":
                    // get all li [editable-area]
                    let options = [];
                    $elem.find( 'li [editable-area]' ).each( function( item ) { options.push( { [ $elem.attr( 'ope-sub-name' ) ] : $this._strip( jQuery( this ).html() ) } ) } );
                    collected = { ...collected, ...{ [ $elem.attr( 'ope-name' ) ] : options } };
                    break;
                default:
                    collected = { ...collected, ...{ [ $elem.attr( 'ope-name' ) ] : $this._strip( $elem.find('[editable-area]').html() ) } };
            }
        });

        return collected;
    },

    /**
     * Debounce to prevent too many calls
     */
    _debounce: function( arg , options ) {

        arg = arg || null;
        options = options || {};

        clearTimeout( this.debounceTimer );
        this.debounceTimer = setTimeout( function() {
            if ( typeof arg == 'function' ) {
                callback();
            } else {
                switch ( arg ) {
                    case 'toggle':
                    default:
                        this.toggleEdit( options );
                }
            }
        }.bind( this ), this.debounceTimeout );

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
        jQuery( 'body' ).trigger( `${this.hookPrefix}${this.uniqueid}.` + hook, args );
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
        jQuery( 'body' ).on( `${this.hookPrefix}${this.uniqueid}.` + hook, callback );
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
        jQuery( 'body' ).off( `${this.hookPrefix}${this.uniqueid}.` + hook, callback );
    },     

};


(function( $ ) {
    $(document).ready( function() {

        onpageeditor = new onPageEditor();

        onpageeditor.addHook( 'success' , function( event, data ) { 
            if ( data.status.code == 200 ) {
                $( '#messagearea' ).text( 'Success! Forwarding you to recipe now!' );
                window.setTimeout( function(){ window.location = data.recipeLink; }.bind( data ) , 1000 );
                //window.location = data.recipeLink;
            } else {
                $( '#messagearea' ).text( data.status.message );
                //alert( data.status.message ); 
            }
        } );
        
        $( '.ope-edittext' ).on( 'click' , function() {
            onpageeditor._debounce( 'toggle' , { toggleButton: this } );
        } );

        onpageeditor.addHook( 'toggle' , function() { 
			$( '.ope-update' ).prop( 'disabled' , (i,v) => !v ) ; 
		} );

        onpageeditor.addHook( 'update-before' , function( e , data ) {
            console.log( data );
        });

        onpageeditor.addHook( 'update-success' , function( e , data ) {
            
        });

        $( '.ope-reverse' ).on( 'click' , function() {
            onpageeditor.move_item(1,0,'down');
        } );  
        
        $( '.ope-handle-up' ).on( 'click' , function() {
             
            console.log( $(this).closest( 'li' ).index());
        } );

        $( '.ope-update' ).on( 'click' , function() {
            let collection = onpageeditor.collect();

			onpageeditor.triggerHook( 'update-before' , [ { collection: collection } ] );

            jQuery.ajax( {
                type: 'POST',
                url: `/wp-admin/admin-ajax.php?action=update_recipe`,
                data: { 
                    postid: $( 'onpageeditor' ).attr( 'postid' ),
                    reciperemix: collection
                },
                success: (data) => {
                    
                    data = JSON.parse( data );

                    onpageeditor.triggerHook( 'update-success' , [ { ...data } ] );
                    
                }
            });

        } );

    });
})(jQuery);

