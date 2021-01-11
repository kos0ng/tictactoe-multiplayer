var rowHTML  = "<div class='row'></div>";
var cellHTML = "<div class='cell'></div>";

var Game = null;

$( function () {
	
	// Un flag pentru a retine daca jocul a inceput sau nu
	var gameStarted = false;
	
	/**
	 * Creaza grila de 3x3 a jocului
	 *
	 * @param grid
	 */
	function createGrid( grid ) {
		var cellId = 0;
		
		// Creare fiecarui rand din grila
		grid.forEach( function ( row ) {
			var rowElem = $( rowHTML );
			$( '.grid' ).append( rowElem );
			
			// Creare fiecarei celule din grila
			row.forEach( function ( cell, index ) {
				var cellObject = new Cell( cell, cellId++ );
				rowElem.append( cellObject.element );
			} )
		} )
	}
	
	/**
	 * O entitate pentru celula din grila jocului
	 *
	 * @param value
	 * @param id
	 * @constructor
	 */
	function Cell( value, id ) {
		var element = $( cellHTML );
		element.attr( 'id', 'cell-' + id );
		element.text( value );
		
		// Evenimetul care se declanseaza in momentul in care
		// utilizatorul da click pe o celula
		element.click( function () {
			var data = { // get row and column for the selected cell
				"_token": $('#token').val(),
				"id": $('#id').val(),
				row    : parseInt( id / 3 ),
				column : parseInt( id % 3 )
			};
			
			// Apel catre server cu datele precum pozitia
			// celulei pe care s-a dat click

			$.post( 'addSign', data, function ( resp ) {
				if ( resp ){
					element.text( resp['sign'] );
					
					// Daca jocl s-a terminat si jucatorul curent este castigator
					// sau s-a ajuns la o egalitate se va afisa
					// blocul cu mesajul propriuzis
					switch ( resp['winner'] ) {
						case 1:
							$( '.win' ).removeClass( 'hide' );
							break;
						case -1:
							$( '.draw' ).removeClass( 'hide' );
							break;
					}
					
					// Opreste functia de actualizare
					if( resp['winner']  == 1 || resp['winner']  == -1 ){
						clearInterval( updater );
					}
					
				}
			} );
		} );
		
		this.element = element;
	}
	
	/**
	 * Functia care se apeleaza la fiecare actualizare a jocului
	 */

	function update() {
		var id=document.getElementById('id').value;
		console.log(id);
		$.get( '/update/'+id, function ( resp ) {
			// Daca jocl s-a terminat si jucatorul curent este castigator
			// sau s-a ajuns la o egalitate se va afisa
			// blocul cu mesajul propriuzis
			Game = resp;
			if(gameStarted==true){
				if ( resp['winner'] == 0){
					$( '.lose' ).removeClass( 'hide' );
					clearInterval( updater );
				}
				switch ( resp['winner'] ) {
				case 1:
					$( '.win' ).removeClass( 'hide' );
					break;
				case -1:
					$( '.draw' ).removeClass( 'hide' );
					break;
			}
			console.log(resp['turn']);
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
			
			// Atunci cand cel de-al doilea jucator intra in camera,
			// initializeaza grila si porneste jocul.
			if ( resp['accepted']=='1' && !gameStarted ) {
				gameStarted = true;
				$( '.loading' ).addClass( 'hide' );
				$( '.grid' ).removeClass( 'hide' );
				createGrid( Game.table_data );
			}
			}
		
			
			// In cazul in care se intra intr-un joc deja inceput
			// se va popula grila cu valorile din baza de date

			
		} );
	}

	function noitfinvite(item, index){
		$('#play'+item['user2_id']).html('Continue');
	}
	function noitfinvited(item, index){
		$('.invite'+item['user1_id']).removeClass('hide');
		$('#play'+item['user1_id']).addClass('hide');
	}
	function notification(){
		$.get( '/notif', function ( resp ) {
			resp[0].forEach(noitfinvite);
			resp[1].forEach(noitfinvited);
		});
	}
	
	// Initializarea jocului:
	// Daca utilizatorul este singur in camera virtuala atunci
	// va astepta un alt jucator sa intre in camera, altfel
	// se va initializa grila si se va incepe jocul
	// var opponent=document.getElementById('opponent').value;
	// $.get( '/play/'+opponent, function ( resp ) {

	// 	data = resp;
	// 	console.log('zzz');
	// 	if ( data.constructor == Array ) {//daca primesc array
	// 		// begin game
	// 		// console.log("begin game ->",data);
	// 		$( '.grid' ).toggleClass( 'hide' );
	// 		createGrid( data );
	// 		$( '.loading' ).addClass( 'hide' );
	// 		gameStarted = true;
	// 	} else {
	// 		// wait for player
	// 		console.log( "wait for player ->" );
	// 	}
		
	// } );
	
	// Initializarea functiei de actualizare la fiecare 500 milisecunde
	if (window.location.href.indexOf("game/") > -1) {
		var updater = setInterval( function () {
		update();
	}, 500 );
	}
	else{
		var updater = setInterval( function () {
		notification();
	}, 500 );
	}
} );

