<?php
function Module_JSON( $db )
{
	$card_id			= Functions::Post_Int( 'card_id' );
	$normal				= Functions::Post_Int( 'normal' );
	$gold				= Functions::Post_Int( 'gold' );

	if ( !Functions::Card_Load_Id( $db, $card_id, $card ) )
	{
		return JSON_Response_Error( '#Error#', 'Failed to load card' );
	}

	if ( $normal < 0 OR !is_int( $normal ) )
	{
		return JSON_Response_Error( '#Error#', 'Invalid normal value' );
	}

	if ( $gold < 0 OR !is_int( $gold ) )
	{
		return JSON_Response_Error( '#Error#', 'Invalid normal value' );
	}

	if ( $card[ 'rarity' ] == 'Legendary' )
	{
		$card[ 'normal' ]	= $normal > 1 ? 1 : $normal;
		$card[ 'gold' ]		= $gold > 1 ? 1 : $gold;
	}
	else
	{
		$card[ 'normal' ]	= $normal > 2 ? 2 : $normal;
		$card[ 'gold' ]		= $gold > 2 ? 2 : $gold;
	}

	if ( !Functions::Card_Update( $db, $card ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success();
}
?>