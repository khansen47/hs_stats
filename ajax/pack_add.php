<?php
function Module_JSON( $db )
{
	$common = Functions::Post_Int( 'common' );
	$c_gold	= Functions::Post_Int( 'c_gold' );
	$rare 	= Functions::Post_Int( 'rare' );
	$r_gold	= Functions::Post_Int( 'r_gold' );
	$epic 	= Functions::Post_Int( 'epic' );
	$e_gold	= Functions::Post_Int( 'e_gold' );
	$legend = Functions::Post_Int( 'legend' );
	$l_gold	= Functions::Post_Int( 'l_gold' );

	if ( !Functions::Pack_Load( $db, $packs ) )
	{
		return JSON_Response_Error( '#Error#', 'Failed to load pack data' );
	}

	$packs[ 'common' ] 	= $packs[ 'common' ] 	+ $common;
	$packs[ 'c_gold' ] 	= $packs[ 'c_gold' ] 	+ $c_gold;
	$packs[ 'rare' ]	= $packs[ 'rare' ] 		+ $rare;
	$packs[ 'r_gold' ]	= $packs[ 'r_gold' ] 	+ $r_gold;
	$packs[ 'epic' ]	= $packs[ 'epic' ] 		+ $epic;
	$packs[ 'e_gold' ]	= $packs[ 'e_gold' ] 	+ $e_gold;
	$packs[ 'legend' ]	= $packs[ 'legend' ] 	+ $legend;
	$packs[ 'l_gold' ]	= $packs[ 'l_gold' ] 	+ $l_gold;
	$packs[ 'opened' ]++;

	if ( ( $common + $c_gold + $rare + $r_gold + $epic + $e_gold + $legend + $l_gold ) != 5 )
	{
		return JSON_Response_Error( '#Error#', 'Not a valid pack' );
	}

	if ( !Functions::Pack_Update( $db, $packs ) )
	{
		return JSON_Response_Global_Error();
	}

	//Update account pack info (perhaps should be in packs)
	if ( !Functions::Account_Load( $db, $account ) )
	{
		return JSON_Response_Error( '#Error#', 'Failed to load account' );
	}

	if ( $legend + $l_gold != 0 ) 	$account[ 'last_l' ] = 0;
	else							$account[ 'last_l' ]++;


	if ( $epic + $e_gold != 0 )		$account[ 'last_e' ] = 0;
	else							$account[ 'last_e' ]++;

	if ( !Functions::Account_Update( $db, $account ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success();
}
?>