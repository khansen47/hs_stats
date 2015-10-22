<?php
function Module_JSON( $db )
{
	$opponent 	= Functions::Post( 'opponent' );
	$win		= Functions::Post_Int( 'win' );
	$coin 		= Functions::Post_Int( 'coin' );

	if ( !Functions::ArenaRun_Load_Active( $db, $arena_run ) )
	{
		return JSON_Response_Error( '#Error#', 'Failed to load active arena' );
	}

	$arena_match[ 'arena_id' ] 	= $arena_run[ 'id' ];
	$arena_match[ 'opponent' ] 	= $opponent;
	$arena_match[ 'win' ] 		= $win;
	$arena_match[ 'coin' ] 		= $coin;

	if ( !Functions::ArenaMatch_Validate( $arena_match ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success();
}
?>