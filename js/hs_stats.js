$( document ).ready( function()
{
	$.fn.json = function( file, variables, callback )
	{
		var data = 'file=' + encodeURIComponent( file ) + ( variables == '' ? '' : '&' + variables );

		$.ajax( '/hs_stats/json.php', {
			type	: 'POST',
			dataType: 'JSON',
			data	:  data,
			success	: function( response )
			{
				callback( response );
			},
			error	: function( jqXHR, textStatus, errorThrown )
			{
				var response 			= new Object();
				response.success		= 0;
				response.error_code		= '#Error#';
				response.error_message	= 'The server returned an invalid response.\n' +
										  'File: ' + file + '\n' +
										  'Response: ' + jqXHR.responseText;
				callback( response );
			}
		} );
	}

	$( "#class_set" ).bind( "change", function()
	{
		window.location.href = 'index.php?Class=' +this.value + '&Missing=' + $( "#missing_select" ).val();
	} );

	$( "#missing_select" ).bind( "change", function()
	{
		window.location.href = 'index.php?Class=' + $( "#class_set" ).val() + '&Missing='+this.value;
	} );

	$( "#update_card_update" ).bind( "click", function()
	{
		var card_id, card_class, normal, gold;

		card_class 	= $( "#class_set" ).val();
		normal 		= $( "#update_card_normal" ).val();
		gold 		= $( "#update_card_gold" ).val();
		card_id 	= $( "#card_options" ).val();

		$.fn.json( 'card_update', $.param( { 'card_id' : card_id, 'normal' : normal, 'gold' : gold } ), function( response )
		{
			if ( !response.success )
			{
				return alert( response.error_message );
			}

			location.reload();
		} );
	} );

	$( "#account_update_save" ).bind( "click", function()
	{
		var dust_am, dust_de, gold;

		dust_am = $( "#account_update_cur_dust" ).val();
		dust_de = $( "#account_update_de_total" ).val();
		gold 	= $( "#account_update_gold_total" ).val();

		$.fn.json( 'account_update', $.param( { 'dust_am' : dust_am, 'dust_de' : dust_de, 'gold' : gold } ), function( response )
		{
			if ( !response.success )
			{
				return alert( response.error_message );
			}

			location.reload();
		} );
	} );

	$( "#pack_add" ).bind( "click", function()
	{
		var common, c_gold, rare, r_gold, epic, e_gold, legend, l_gold;

		common 	= $( "#pack_add_common" ).val();
		c_gold 	= $( "#pack_add_c_gold" ).val();
		rare 	= $( "#pack_add_rare" ).val();
		r_gold 	= $( "#pack_add_r_gold" ).val();
		epic 	= $( "#pack_add_epic" ).val();
		e_gold 	= $( "#pack_add_e_gold" ).val();
		legend 	= $( "#pack_add_legend" ).val();
		l_gold 	= $( "#pack_add_l_gold" ).val();

		$.fn.json( 'pack_add', $.param( { 'common' 	: common,
										  'c_gold' 	: c_gold,
										  'rare' 	: rare,
										  'r_gold'	: r_gold,
										  'epic' 	: epic,
										  'e_gold' 	: e_gold,
										  'legend' 	: legend,
										  'l_gold' 	: l_gold } ),
		function( response )
		{
			if ( !response.success )
			{
				return alert( response.error_message );
			}

			location.reload();
		} );
	} );

	$( '#card_tbody tr' ).click( function()
	{
		$( '#card_options' ).val( $( this ).attr( 'card_id' ) );

		$( '#update_card_normal' ).val( $( 'option:selected', '#card_options' ).attr( 'card_normal' ) );
		$( '#update_card_gold' ).val( $( 'option:selected', '#card_options' ).attr( 'card_gold' ) );
	} );

	$( '#card_options' ).bind( 'change', function()
	{
		alert( $('option:selected', this).attr('card_normal') );
		$( '#update_card_normal' ).val( $('option:selected', this).attr('card_normal') );
		$( '#update_card_gold' ).val( $('option:selected', this).attr('card_gold') );
		//select row with matching id
		//update how update card works
	} );


	//Arena JS
	$( "#add_match_add" ).bind( "click", function()
	{
		var i, radios, opponent, win, coin;

		radios 	= document.getElementsByName( 'class_radio' );

		for ( i = 0; i < radios.length; i++ )
		{
		    if ( radios[i].checked )
		    {
		        opponent = radios[i].value;
		        break;
		    }
		}

		dialog = createDialog( 'TEST', 'NOW' );


		win 	= $( "#add_match_win" ).is( ':checked' ) ? 1 : 0;
		coin 	= $( "#add_match_coin" ).is( ':checked' ) ? 1 : 0;

		$.fn.json( 'match_add', $.param( { 'opponent'	: opponent,
										   'win' 		: win,
										   'coin'		: coin } ),
		function( response )
		{
			if ( !response.success )
			{
				return alert( response.error_message );
			}

			location.reload();
		} );
		
	} );

	function createDialog( title, text )
	{
	    return $( "<div class='dialog' title='" + title + "'><p>" + text + "</p></div>" ).dialog();
	}
} );