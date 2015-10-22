<?php
/*
INFO ON WHAT THIS PAGE WILL SHOW

Graphs of all my arena stats
Need to do importing of all my old arena runs
New feature of arena (how many of which rarity rarity)

*/

include_once( "classes/functions.php" );
include_once( "classes/layout.php" );

$functions 	= new Functions();
$layout 	= new Layout();
$database2 	= new Database2();

$layout->title("HS Arena stats");
$layout->header();

$arena_id 	= Functions::Post_Int( 'ArenaID' );
$new_arena 	= 0;

Functions::ClassList_Load_Classes( $database2, $classes );

if ( !Functions::ArenaRun_Load_ID( $database2, $arena_id, $arena_run ) )
{
	if ( !Functions::ArenaRun_Load_Active( $database2, $arena_run ) )
	{
		$new_arena = 1;
	}
}

if ( !$new_arena )
{
	Functions::ArenaRewards_Load_ArenaID( 	$database2, $arena_run[ 'id' ], $arena_rewards );
	Functions::ArenaMatchList_Load_ArenaID( $database2, $arena_run[ 'id' ], $arena_matches );
}

?>
<a href="index.php">Home</a>
<h1 style="margin: 5px;">Arena Stats and Data</h1>

<!-- Stats -->
<div style="float: left;">
	<h2 style="margin-left: 20px;">Stats</h2>
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-left: 20px;">
		<thead>
			<tr><th colspan="2">Overall Stats</th></tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Win Rate</b></td>
				<td>0</td>
			</tr>
			<tr>
				<td><b>Average Run Length</b></td>
				<td>0</td>
			</tr>
			<tr>
				<td><b>Average Gold Reward</b></td>
				<td>0</td>
			</tr>
			<tr>
				<td><b># of Arenas</b></td>
				<td>0</td>
			</tr>
			<tr>
				<td><b>Arena Games Played</b></td>
				<td>0</td>
			</tr>
		</tbody>
	</table>

	<table cellpadding="2" cellspacing="0" style="width: auto; margin-left: 20px;">
		<thead>
			<tr><th colspan="8">Overall Class Stats</th></tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Class</b></td>
				<td><b>Runs</b></td>
				<td><b>Win %</b></td>
				<td><b>Average Run Length</b></td>
				<td><b>Coin Win %</b></td>
				<td><b>W/O Coin Win %</b></td>
				<td><b>Best Opponent</b></td>
				<td><b>Worst Opponent</b></td>
			</tr>
			<tr>
				<td>Druid</td>
				<td>37</td>
				<td>63%</td>
				<td>5.12</td>
				<td>64%</td>
				<td>55%</td>
				<td>Rogue</td>
				<td>Mage</td>
			</tr>
		</tbody>
	</table>
</div>

<?php if ( $new_arena ) { ?>
<!-- New Run -->
<div id="new_run" style="float: left;">
	<h2 style="margin-left: 20px;">New Run</h2>
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-left: 20px;">
		<thead>
			<tr><th colspan="6">New Run</th></tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Class</b></td>
				<td align="right">
					<?php
						foreach ( $classes as $key => $class )
						{
							echo "<input name='new_run_class_radio' type='radio' value='".$class[ 'name' ]."' id='".$class[ 'name' ]."' /><label for='".$class[ 'name' ]."'>".$class[ 'name' ]."</label><br />";
						}
					?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" id="new_run_insert" value="Create" /></td>
			</tr>
		</tbody>
	</table>
</div>
<?php } else { ?>
<!-- Current Run -->
<div id="active_run" style="float: left;">
	<h2 style="margin-left: 20px;">Current Run</h2>

	<input style="float: right;" type="button" value="Save Run" id="arena_run_close" />

	<table cellpadding="2" cellspacing="0" style="width: auto; margin-left: 20px;">
		<thead>
			<tr><th colspan="6">Add Match</th></tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Opponent</b></td>
				<td><b>Win</b></td>
				<td><b>Coin</b></td>
				<td>&nbsp</td>
				<td>&nbsp</td>
				<td>
					<input type="button" value="Retire" id="arena_run_retire" />
				</td>
			</tr>
			<tr>
				<td>
					<?php
						foreach ( $classes as $key => $class )
						{
							echo "<input name='class_radio' type='radio' value='".$class[ 'name' ]."' id='".$class[ 'name' ]."' /><label for='".$class[ 'name' ]."'>".$class[ 'name' ]."</label><br />";
						}
					?>
				</td>
				<td valign="top">
					<input type="checkbox" id="add_match_win" />
				</td>
				<td valign="top">
					<input type="checkbox" id="add_match_coin" />
				</td>
				<td valign="top">
					<input type="button" value="Add" id="add_match_add" />
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</tbody>
	</table>

	<h2 style="margin-left: 20px;">Matches</h2>
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-left: 20px;">
		<thead>
			<tr><th colspan="8">Arena Matches</th></tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Opponent</b></td>
				<td><b>Win</b></td>
				<td><b>Coin</b></td>
				<td><b>Delete</b></td>
			</tr>
			<tr>
				<td>Druid</td>
				<td>Win</td>
				<td>Coin</td>
				<td><a>Delete</a></td>
			</tr>
		</tbody>
	</table>

	<h2 style="margin-left: 20px;">Rewards</h2>
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-left: 20px;">
		<thead>
			<tr><th colspan="6">Arena Rewards</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>Gold:</td>
				<td><input type="text" id="arena_reward_gold" value="0" /></td>
			</tr>
			<tr>
				<td>Dust:</td>
				<td><input type="text" id="arena_reward_dust" value="0" /></td>
			</tr>
			<tr>
				<td>Packs:</td>
				<td><input type="text" id="arena_reward_packs" value="0" /></td>
			</tr>
			<tr>
				<td>Normal Cards:</td>
				<td><input type="text" id="arena_reward_normal_cards" value="0" /></td>
			</tr>
			<tr>
				<td>Gold Cards:</td>
				<td><input type="text" id="arena_reward_gold_cards" value="0" /></td>
			</tr>
		</tbody>
	</table>


	<h2 style="margin-left: 20px;">Notes</h2>
	<textarea style="margin-left: 20px;" id="arena_run_notes" rows="8" cols="50"></textarea>
</div>
<?php } ?>

<?php
$layout->footer();
?>