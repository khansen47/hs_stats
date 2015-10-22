<?php
function Module_JSON( $db )
{
	$report_id		= Functions::Post_Int( 'report_id' );
	$project_id		= Functions::Post_Int( 'project_id' );
	$action			= Functions::Post( 'action' );
	$date_input		= Functions::Post( 'date' );
	$employee_id	= Functions::Post_Int( 'employee_id' );
	$hours			= Functions::Post( 'time' );
	$description	= Functions::Post( 'description' );
	$billable		= Functions::Post_Active( 'billable' );
	$custom_rate	= Functions::Post_Active( 'custom_rate' );

	if ( !Functions::Report_Load( $db, $report_id, $loaded_report ) )
	{
		return JSON_Response_Error( '#Error#', 'Report not found' );
	}

	if ( !Functions::Project_Load( $db, $project_id, $loaded_project ) )
	{
		return JSON_Response_Error( '#Error#', 'Project not found' );
	}

	if ( !Functions::ReportProject_Load_ReportProject( $db, $report_id, $project_id, $loaded_report_project ) )
	{
		return JSON_Response_Error( '#Error#', 'Report project not found' );
	}

	if ( $custom_rate === 1 && $loaded_report_project[ 'custom_rate_id' ] === 1 )
	{
		return JSON_Response_Error( '#Error#', 'You cannot set this item to have a custom rate, until you select a custom rate type' );
	}

	if ( $date_input == '' )
	{
		return JSON_Response_Error( '#Error#', 'Please select a date' );
	}

	$item_date 	= new DateTime( $date_input );
	$date_from	= new DateTime( $loaded_report[ 'date_from' ] );
	$date_to	= new DateTime( $loaded_report[ 'date_to' ] );

	if ( $item_date < $date_from || $item_date > $date_to )
	{
		return JSON_Response_Error( '#Error#', 'The date range of the item does not fall within the date range of the report' );
	}

	if ( !is_numeric( $hours ) || $hours < 0 )
	{
		return JSON_Response_Error( '#Error#', 'Hours must be positive' );
	}

	if ( $description == '' )
	{
		return JSON_Response_Error( '#Error#', 'Description cannot be blank' );
	}

	switch( $employee_id )
	{
		case 0: // fee
			$type 			= 1;
			$employee_id 	= 0;
			$hours 			= number_format($hours, 2);
			break;

		case 1: // credit - web
			$type 			= 2;
			$employee_id 	= 0;
			$hours 			= Functions::format_hour($hours);
			break;
		case 2: // credit - junior
			$type 			= 3;
			$employee_id 	= 0;
			$hours 			= Functions::format_hour($hours);
			break;
		case 3: // credit - cust
			$type 			= 4;
			$employee_id 	= 0;
			$hours 			= Functions::format_hour($hours);
			break;

		default:
			$type			= 0;
			$hours 			= Functions::format_hour($hours);
		break;
	}

	if ( $type == 0 && !Functions::Employee_Load( $db, $employee_id, $null ) )
	{
		return JSON_Response_Error( '#Error#', 'Employee could not be found' );
	}

	if ( $action === 'add' )
	{
		return Add_Item( $db, $type, $date_input, $employee_id, $hours, $description, $billable, $custom_rate, $loaded_report_project[ 'id' ] );
	}
	else if ( $action === 'update' )
	{
		return Update_Item( $db, $type, $date_input, $employee_id, $hours, $description, $billable, $custom_rate );
	}

	return JSON_Response_Error( '#Error#', 'Invalid action' );

}

function Add_Item( &$db, $type, $date_input, $employee_id, $hours, $description, $billable, $custom_rate, $rp_id )
{
	$item[ 'date' ] 		= date( 'Y-m-d', strtotime( $date_input ) );
	$item[ 'type' ]			= $type;
	$item[ 'hours' ] 		= $hours;
	$item[ 'employee_id' ] 	= $employee_id;
	$item[ 'description' ] 	= $description;
	$item[ 'billable' ]		= $billable;
	$item[ 'custom_rate' ]	= $custom_rate;
	$item[ 'rp_id' ]		= $rp_id;

	if ( !Functions::Item_Insert( $db, $item ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success( array( 'real_id' => $db->insert_id ) );
}

function Update_Item( &$db, $type, $date_input, $employee_id, $hours, $description, $billable, $custom_rate )
{
	$item_id = Functions::Post_Int( 'item_id' );

	if ( !Functions::Item_Load( $db, $item_id, $loaded_item ) )
	{
		return JSON_Response_Error( '#Error#', 'Item could not be found' );
	}

	$item[ 'id' ] 			= $loaded_item[ 'id' ];
	$item[ 'rp_id' ]		= $loaded_item[ 'rp_id' ];
	$item[ 'date' ] 		= date( 'Y-m-d', strtotime( $date_input ) );
	$item[ 'type' ]			= $type;
	$item[ 'hours' ] 		= $hours;
	$item[ 'employee_id' ] 	= $employee_id;
	$item[ 'description' ] 	= $description;
	$item[ 'billable' ]		= $billable;
	$item[ 'custom_rate' ]	= $custom_rate;

	if ( !Functions::Item_Update( $db, $item ) )
	{
		return JSON_Response_Global_Error();
	}

	return JSON_Response_Success( array( 'real_id' => $item_id ) );
}
?>