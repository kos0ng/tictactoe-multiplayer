var rowHTML  = "<div class='row'></div>";
var cellHTML = "<div class='cell'></div>";

var Game = null;

function decline(id){
            if (confirm('Are you sure to decline invitation??')) {
                $.get("/decline/"+id,function(result){   
                    location.reload();
                });
            }
    }

$( function () {
	
	var gameStarted = false;
	
	/**
	 *
	 * @param grid
	 */
	function createGrid( grid ) {
		var cellId = 0;
		
		grid.forEach( function ( row ) {
			var rowElem = $( rowHTML );
			$( '.grid' ).append( rowElem );
			
			row.forEach( function ( cell, index ) {
				var cellObject = new Cell( cell, cellId++ );
				rowElem.append( cellObject.element );
			} )
		} )
	}
	
	/**
	 *
	 * @param value
	 * @param id
	 * @constructor
	 */
	function Cell( value, id ) {
		var element = $( cellHTML );
		element.attr( 'id', 'cell-' + id );
		element.text( value );
		
		element.click( function () {
			var data = { 
				"_token": $('#token').val(),
				"id": $('#id').val(),
				row    : parseInt( id / 3 ),
				column : parseInt( id % 3 )
			};
			

			$.post( 'addSign', data, function ( resp ) {
				if ( resp ){
					element.text( resp['sign'] );
					
					switch ( resp['winner'] ) {
						case 1:
							$( '.win' ).removeClass( 'hide' );
							$( '.turn1' ).addClass( 'hide' );
							$( '.turn2' ).addClass( 'hide' );
							break;
						case -1:
							$( '.draw' ).removeClass( 'hide' );
							$( '.turn1' ).addClass( 'hide' );
							$( '.turn2' ).addClass( 'hide' );
							break;
					}
					
					if( resp['winner']  == 1 || resp['winner']  == -1 ){
						clearInterval( updater );
					}
					
				}
			} );
		} );
		
		this.element = element;
	}
	
	function update() {
		var id=document.getElementById('id').value;
		// console.log(id);
		$.get( '/update/'+id, function ( resp ) {

			Game = resp;
			if(gameStarted==true){
				if ( resp['winner'] == 0){
					$( '.lose' ).removeClass( 'hide' );
					$( '.turn1' ).addClass( 'hide' );
					$( '.turn2' ).addClass( 'hide' );
					clearInterval( updater );
				}
				switch ( resp['winner'] ) {
				case 1:
					$( '.win' ).removeClass( 'hide' );
					$( '.turn1' ).addClass( 'hide' );
					$( '.turn2' ).addClass( 'hide' );
					break;
				case -1:
					$( '.draw' ).removeClass( 'hide' );
					$( '.turn1' ).addClass( 'hide' );
					$( '.turn2' ).addClass( 'hide' );
					break;
			}
			// console.log(resp['turn']);
			switch ( resp['turn'] ) {
				case 1:
					$( '.turn1' ).removeClass( 'hide' );
					$( '.turn2' ).addClass( 'hide' );
					break;
				case 2:
					$( '.turn2' ).removeClass( 'hide' );
					$( '.turn1' ).addClass( 'hide' );
					break;
			}
			
			if( resp['winner']  == 1 || resp['winner']  == -1 ){
				clearInterval( updater );
			}
			var cellId = 0;
			Game.table_data.forEach( function ( row ) {
				row.forEach( function ( cell, index ) {
					$( '#cell-' + cellId ).html( cell );
					cellId++;
				} )
			} )

			}
			else{
			
			if ( resp['accepted']=='1' && !gameStarted ) {
				gameStarted = true;
				$( '.loading' ).addClass( 'hide' );
				$( '.grid' ).removeClass( 'hide' );
				createGrid( Game.table_data );
			}
			if(resp==3){
				alert('Invitiation Declined!');
				window.location.replace("/home");
			}
			}
		
			
		} );
	}

	function notifoffline(item, index){
		$('#offline'+item).removeClass('hide');
		$('#online'+item).addClass('hide');
	}

	function notifonline(item, index){
		$('#offline'+item).addClass('hide');
		$('#online'+item).removeClass('hide');
	}

	function notifinvite(item, index){
		$('#offline'+item['user2_id']).addClass('hide');
		$('#online'+item['user2_id']).removeClass('hide');
		$('#play'+item['user2_id']).html('Continue').removeClass('btn-success').addClass('btn-warning');
		}
	
	function notifinvited(item, index){
		$('#offline'+item['user1_id']).addClass('hide');
		$('#online'+item['user1_id']).removeClass('hide');
		if(item['accepted']==1){
			$('#play'+item['user1_id']).html('Continue').removeClass('btn-success').addClass('btn-warning');		
			$('.invite'+item['user1_id']).addClass('hide');
		}
		else{
			$('.invite'+item['user1_id']).removeClass('hide');
			$('#play'+item['user1_id']).addClass('hide');
		}
	}
	function notification(){
		$.get( '/notif', function ( resp ) {
			resp[2].forEach(notifoffline);
			resp[3].forEach(notifonline);
			resp[0].forEach(notifinvite);
			resp[1].forEach(notifinvited);
		});
	}

	
	if (window.location.href.indexOf("game/") > -1) {
		var updater = setInterval( function () {
		update();
	}, 500 );
	}
	else{
		if(location.pathname=='/home'){
					var updater = setInterval( function () {
				notification();
			}, 500 );
		}
	}
} );

