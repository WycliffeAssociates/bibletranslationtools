var FLBuilderNumber;

(function($) {
	
	/**
	 * Class for Number Counter Module
	 *
	 * @since 1.6.1
	 */
	FLBuilderNumber = function( settings ){

		// set params
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .fl-number';
		this.layout				 = settings.layout;
		this.type				 = settings.type;
		this.number				 = settings.number;
		this.max				 = settings.max;
		this.speed 				 = settings.speed;
		this.delay 				 = settings.delay;
		this.breakPoints         = settings.breakPoints;
		this.currentBrowserWidth = $( window ).width();
		this.animated            = false;
		this.format 			 = settings.format;

		// initialize the menu 
		this._initNumber();
		
	};
	
	FLBuilderNumber.prototype = {
		nodeClass               : '',
		wrapperClass            : '',
		layout 	                : '',
		type 	                : '',
		number 	                : 0,
		max 	                : 0,
		speed 					: 0,
		delay 					: 0,
		format 					: {},

		_initNumber: function(){

			var self = this;

			if( typeof jQuery.fn.waypoint !== 'undefined' ) {
				$( this.wrapperClass ).waypoint({
					offset: FLBuilderLayoutConfig.waypoint.offset + '%',
					triggerOnce: true,
					handler: function( direction ){
						self._initCount();
					}
				});
			} else {
				self._initCount();
			}

		},

		_initCount: function(){

			var $number = $( this.wrapperClass ).find( '.fl-number-string' );

			if( !isNaN( this.delay ) && this.delay > 0 ) {
				setTimeout( function(){
					if( this.layout == 'circle' ){
						this._triggerCircle();
					} else if( this.layout == 'bars' ){
						this._triggerBar();
					}
					this._countNumber();
				}.bind( this ), this.delay * 1000 );
			}
			else {
				if( this.layout == 'circle' ){
					this._triggerCircle();
				} else if( this.layout == 'bars' ){
					this._triggerBar();
				}
				this._countNumber();
			}
		},

		_countNumber: function(){

			var $number = $( this.wrapperClass ).find( '.fl-number-string' ),
				$string = $number.find( '.fl-number-int' ),
				current = 0,
				self    = this;

			if ( ! this.animated ) {
			    $string.prop( 'Counter',0 ).animate({
			        Counter: this.number
			    }, {
			        duration: this.speed,
			        easing: 'swing',
			        step: function ( now, fx ) {
			        	$string.text( self._formatNumber( Math.ceil(now) ) );
			        },
			        complete: function() {
			        	self.animated = true;
			        }
			    });
			}
		},

		_triggerCircle: function(){

			var $bar   = $( this.wrapperClass ).find( '.fl-bar' ),
				r      = $bar.attr('r'),
				circle = Math.PI*(r*2),
				val    = this.number,
				max    = this.type == 'percent' ? 100 : this.max;
   
			if (val < 0) { val = 0;}
			if (val > max) { val = max;}
			
			if( this.type == 'percent' ){
				var pct = ( ( 100 - val ) /100) * circle;			
			} else {
				var pct = ( 1 - ( val / max ) ) * circle;
			}

		    $bar.animate({
		        strokeDashoffset: pct
		    }, {
		        duration: this.speed,
		        easing: 'swing'
		    });
			
		},

		_triggerBar: function(){

			var $bar = $( this.wrapperClass ).find( '.fl-number-bar' );

			if( this.type == 'percent' ){
				var number = this.number > 100 ? 100 : this.number;
			} else {
				var number = Math.ceil( ( this.number / this.max ) * 100 );
			}

		    $bar.animate({
		        width: number + '%'
		    }, {
		        duration: this.speed,
		        easing: 'swing'
		    });

		},

		_formatNumber: function( n ){
			var rgx	= /(\d+)(\d{3})/;

            n += '';
            x  = n.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? parseFloat(parseFloat('.' + x[1]).toFixed(2)) : '';
			x2 = x2 ? this.format.decimal + x2.toString().split('.').pop() : '';
			
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + this.format.thousands_sep + '$2');
			}
			
			return x1 + x2;
		},
	};
		
})(jQuery);