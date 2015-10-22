<?php
function Module_JSON( $db )
{
	$dust_am	= Functions::Post_Int( 'dust_am' );
	$dust_de	= Functions::Post_Int( 'dust_de' );
	$gold 		= Functions::Post_Int( 'gold' );

	if ( !Functions::Account_Load( $db, $account ) )
	{
		return JSON_Response_Error( '#Error#', 'Failed to load account' );
	}

	$account[ 'dust_am' ] 	= $dust_am;
	$account[ 'dust_de' ] 	= $dust_de;
	$account[ 'gold' ]		= $gold;

	if ( !Functions::Account_Update( $db, $account ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success();
}
?>