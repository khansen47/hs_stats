<?php
//Notes/TODO List
/*
	- Select to further sort cards
		- missing playable
		- missing golden
		- missing normal
	- Split, Cards/Stats/Add/Update to their own pages
	- Adding Packs should be changed (right now no way to fix a bad mistake of adding packs)
	- Change to be a more organized list with better filters
	- Improve the Number of cards By set
		- include rarity percentage complete
		- include dust required to complete set
*/

include_once("classes/functions.php");
include_once("classes/layout.php");

$functions 	= new Functions();
$layout 	= new Layout();
$database2 	= new Database2();

$layout->title("HS Card Stats");
$layout->header();

$class 		= Functions::Get( 'Class' );
$missing 	= Functions::Get( 'Missing' );

if ( $class == '' ) 								$class 		= 'Neutral';
if ( $missing == '' || $missing == 'undefined' ) 	$missing 	= 'None';

Functions::Account_Load( $database2, $account );
Functions::ClassList_Load( $database2, $classes );
Functions::SetList_Load( $database2, $card_sets );
Functions::RarityList_Load_All( $database2, $rarities );

if ( !Functions::Class_Load_Name( $database2, $class, $null ) )
{
	if ( !Functions::Rarity_Load_Name( $database2, $class, $null ) )
	{
		if ( !Functions::Set_Load_Name( $database2, $class, $null ) )
		{
			$class 	= 'Neutral';
			$type 	= 'class';
		}
		else
		{
			$type 	= 'set';
		}
	}
	else
	{
		$type 		= 'rarity';
	}
}
else
{
	$type = 'class';
}

if ( $class == 'Missing' )
{
	Functions::CardList_Load_Missing( $database2, $cards );
}
else
{
	if ( $type == 'class' )
	{
		if ( $missing == 'None' )			Functions::Cards_Load_Class( $database2, $class, $cards );
		else if ( $missing == 'Playable' )	Functions::CardList_Load_Class_MissingPlayable( $database2, $class, $cards );
		else if ( $missing == 'Normal' )	Functions::CardList_Load_Class_MissingNormal( $database2, $class, $cards );
		else if ( $missing == 'Golden' )	Functions::CardList_Load_Class_MissingGolden( $database2, $class, $cards );
		else								Functions::Cards_Load_Class( $database2, $class, $cards );
	}
	else if ( $type == 'rarity' )
	{
		if ( $missing == 'None' )			Functions::Cards_Load_Rarity( $database2, $class, $cards );
		else if ( $missing == 'Playable' )	Functions::CardList_Load_Rarity_MissingPlayable( $database2, $class, $cards );
		else if ( $missing == 'Normal' )	Functions::CardList_Load_Rarity_MissingNormal( $database2, $class, $cards );
		else if ( $missing == 'Golden' )	Functions::CardList_Load_Rarity_MissingGolden( $database2, $class, $cards );
		else								Functions::Cards_Load_Rarity( $database2, $class, $cards );
	}
	else if ( $type == 'set' )
	{
		if ( $missing == 'None' )			Functions::Cards_Load_Set( $database2, $class, $cards );
		else if ( $missing == 'Playable' )	Functions::CardList_Load_Set_MissingPlayable( $database2, $class, $cards );
		else if ( $missing == 'Normal' )	Functions::CardList_Load_Set_MissingNormal( $database2, $class, $cards );
		else if ( $missing == 'Golden' )	Functions::CardList_Load_Set_MissingGolden( $database2, $class, $cards );
		else								Functions::Cards_Load_Set( $database2, $class, $cards );
	}
}


Functions::Cards_Convert_Stats( $cards, $card_stats );

Functions::CardList_Load_All( $database2, $all_cards );
Functions::Cards_Convert_All_Stats( $all_cards, $stats );

Functions::Cards_Load_Rarity( $database2,	'Basic',		$basic );
Functions::Cards_Load_Rarity( $database2,	'Common',		$common );
Functions::Cards_Load_Rarity( $database2,	'Rare',			$rare );
Functions::Cards_Load_Rarity( $database2,	'Epic',			$epic );
Functions::Cards_Load_Rarity( $database2,	'Legendary',	$legendary );
Functions::Cards_Convert_Stats( $basic, 	$basic_stats );
Functions::Cards_Convert_Stats( $common, 	$common_stats );
Functions::Cards_Convert_Stats( $rare, 		$rare_stats );
Functions::Cards_Convert_Stats( $epic, 		$epic_stats );
Functions::Cards_Convert_Stats( $legendary, $legendary_stats );
Functions::Cards_Convert_Rarity_Stats( $basic, $common, $rare, $epic, $legendary, $rarity_dust );

$stats[ 'total_dust' ] = $rarity_dust[ 'basic_dust' ] + $rarity_dust[ 'common_dust' ] + $rarity_dust[ 'rare_dust' ] + $rarity_dust[ 'epic_dust' ] + $rarity_dust[ 'legend_dust' ];

Functions::Pack_Load( $database2, $packs );
Functions::Pack_Convert_Stats( $stats, $packs, $account, $pack_stats );

?>
<a href="arena.php">Arena</a>
<!-- Stats -->
<div style="float: left;">
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr><th colspan=2>Dust Needed To Complete Playable Collection</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>Basic</td>
				<td><?php echo number_format( $rarity_dust[ 'basic_dust' ] ); ?></td>
			</tr>
			<tr>
				<td>Common</td>
				<td><?php echo number_format( $rarity_dust[ 'common_dust' ] ); ?></td>
			</tr>
			<tr>
				<td>Rare</td>
				<td><?php echo number_format( $rarity_dust[ 'rare_dust' ] ); ?></td>
			</tr>
			<tr>
				<td>Epic</td>
				<td><?php echo number_format( $rarity_dust[ 'epic_dust' ] ); ?></td>
			</tr>
			<tr>
				<td>Legendary</td>
				<td><?php echo number_format( $rarity_dust[ 'legend_dust' ] ); ?></td>
			</tr>
			<tr>
				<td>Total</td>
				<td><?php echo number_format( $stats[ 'total_dust' ] ); ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Dust Needed Golden Collection</td>
				<td><?php echo number_format( $stats[ 'golden_dust_needed' ] ); ?></td>
			</tr>
		</tbody>
	</table>

	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr><th colspan=2>My Account</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>Current</td>
				<td><?php echo number_format( $account[ 'dust_am' ] ); ?></td>
			</tr>
			<tr>
				<td>D/E</td>
				<td><?php echo number_format( $account[ 'dust_de' ] ); ?></td>
			</tr>
			<tr>
				<td>Total Dust</td>
				<td><?php echo number_format( $account[ 'dust_am' ] + $account[ 'dust_de' ] ); ?></td>
			</tr>
			<tr>
				<td>Gold</td>
				<td><?php echo number_format( $account[ 'gold' ] ); ?></td>
			</tr>
			<tr>
				<td>Golden D/E</td>
				<td><?php echo number_format( $stats[ 'gold_de_total' ] ); ?></td>
			</tr>
			<tr>
				<td>Normal D/E</td>
				<td><?php echo number_format( $stats[ 'normal_de_total' ] ); ?></td>
			</tr>
		</tbody>
	</table>

	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr><th colspan=4>Pack Data</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<td>Normal</td>
				<td>Golden</td>
				<td>Total</td>
			</tr>
			<tr>
				<td>Common</td>
				<td><?php echo number_format( 100 * $pack_stats[ 'common_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * $pack_stats[ 'c_gold_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * ( $pack_stats[ 'common_percent' ] + $pack_stats[ 'c_gold_percent' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td>Rare</td>
				<td><?php echo number_format( 100 * $pack_stats[ 'rare_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * $pack_stats[ 'r_gold_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * ( $pack_stats[ 'rare_percent' ] + $pack_stats[ 'r_gold_percent' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td>Epic</td>
				<td><?php echo number_format( 100 * $pack_stats[ 'epic_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * $pack_stats[ 'e_gold_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * ( $pack_stats[ 'epic_percent' ] + $pack_stats[ 'e_gold_percent' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td>Legendary</td>
				<td><?php echo number_format( 100 * $pack_stats[ 'legend_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * $pack_stats[ 'l_gold_percent' ], 2 )."%"; ?></td>
				<td><?php echo number_format( 100 * ( $pack_stats[ 'legend_percent' ] + $pack_stats[ 'l_gold_percent' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp</td>
				<td><?php echo number_format( 100 * ( $pack_stats[ 'common_percent' ] + $pack_stats[ 'c_gold_percent' ] + $pack_stats[ 'rare_percent' ] + $pack_stats[ 'r_gold_percent' ] +$pack_stats[ 'epic_percent' ] + $pack_stats[ 'e_gold_percent' ]+ $pack_stats[ 'legend_percent' ] + $pack_stats[ 'l_gold_percent' ] ), 2 )."%"; ?></td>
			</tr>
		</tbody>
	</table>

	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr><th colspan=2>Pack Stats</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>Average Dust Per Pack</td>
				<td><?php echo number_format( $pack_stats[ 'average_dust' ], 2 ); ?></td>
			</tr>
			<tr>
				<td>Average Dust Per Pack with Missing Card</td>
				<td><?php echo number_format( $pack_stats[ 'dust_miss_card' ], 2 ); ?></td>
			</tr>
			<tr>
				<td>Packs Opened</td>
				<td><?php echo number_format( $packs[ 'opened' ] ); ?></td>
			</tr>
			<tr>
				<td>Packs to Buy</td>
				<td><?php echo number_format( $pack_stats[ 'packs_to_buy' ] ); ?></td>
			</tr>
			<tr>
				<td>Packs to Buy with Missing Card Chance</td>
				<td><?php echo number_format( $pack_stats[ 'packs_to_buy_miss' ] ); ?></td>
			</tr>
			<tr>
				<td>Packs to Buy w/ missing AND gold DE</td>
				<td><?php echo number_format( $pack_stats[ 'packs_to_buy_gold_de' ] ); ?></td>
			</tr>
			<tr>
				<td>Packs to Buy To Complete Golden Set</td>
				<td><?php echo number_format( $pack_stats[ 'packs_to_buy_golden' ] ); ?></td>
			</tr>
			<tr>
				<td>Last Pack with an Epic</td>
				<td><?php echo $account[ 'last_e' ]; ?></td>
			</tr>
			<tr>
				<td>Last Pack with a Legendary</td>
				<td><?php echo $account[ 'last_l' ]; ?></td>
			</tr>
		</tbody>
	</table>
</div>

<!-- CARDS -->
<div style="float: left;">
	<div class="styled-select slate" style="margin-top: 20px;">
		<select id="class_set" autofocus>
		<?php
			echo "<option disabled>-- Class --</option>";

			foreach ( $classes as $key => $class_value )
			{
				echo "<option value='".$class_value[ 'name' ]."'";

				if ( $class_value[ 'name'] == $class )	echo " selected";

				echo ">".$class_value[ 'name' ]."</option>";
			}

			echo "<option disabled>-- Rarity --</option>";
			foreach ( $rarities as $key => $rarity )
			{
				echo "<option value='".$rarity[ 'name' ]."'";

				if ( $rarity[ 'name' ] == $class ) echo " selected";

				echo ">".$rarity[ 'name' ]."</option>";
			}

			echo "<option disabled>-- Card Set --</option>";
			foreach ( $card_sets as $key => $set )
			{
				echo "<option value='".$set[ 'name' ]."'";

				if ( $set[ 'name' ] == $class ) echo " selected";

				echo ">".$set[ 'name' ]."</option>";
			}
		?>
		</select>
	</div>
	<?php if ( $class != 'Missing' ): ?>
	<div class="styled-select slate" style="margin-top: 20px;">
		<select id="missing_select">
			<?php
				if ( $missing == 'None' ) 		echo "<option value='None' selected>All Cards</option>";
				else							echo "<option value='None'>All Cards</option>";

				if ( $missing == 'Playable' ) 	echo "<option value='Playable' selected>Missing Playable</option>";
				else							echo "<option value='Playable'>Missing Playable</option>";

				if ( $missing == 'Normal' ) 	echo "<option value='Normal' selected>Missing Normal</option>";
				else							echo "<option value='Normal'>Missing Normal</option>";

				if ( $missing == 'Golden' ) 	echo "<option value='Golden' selected>Missing Golden</option>";
				else							echo "<option value='Golden'>Missing Golden</option>";
			?>
		</select>
	</div>
	<?php endif; ?>
	<div id="card_table" style="display: inline;">
		<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
			<thead>
				<tr>
					<th>Mana</th>
					<th>Name</th>
					<th>Normal</th>
					<th>Golden</th>
					<th>Class</th>
					<th>Set</th>
				</tr>
			</thead>
			<tbody id="card_tbody">
			<?php foreach ( $cards as $key => $card ):
				if ( $card['rarity'] == 'Basic' ) 			$set_style 	= "bgcolor='#BDBDBD'";
				else if ( $card['rarity'] == 'Common' ) 	$set_style 	= "bgcolor='#FFF'";
				else if ( $card['rarity'] == 'Rare' ) 		$set_style 	= "bgcolor='#198EFF'";
				else if ( $card['rarity'] == 'Epic' ) 		$set_style 	= "bgcolor='#AB48EE'";
				else if ( $card['rarity'] == 'Legendary' ) $set_style 	= "bgcolor='#ff6b00'";

				$image_url = 'https://s3-us-west-2.amazonaws.com/hearthstats/cards/' . strtoloweR( str_replace( ' ', '-', $card[ 'name' ] ) ) . '.png';
			?>
				<tr <?php echo $set_style; ?> card_id="<?php echo $card[ 'id' ]; ?>">
					<td <?php echo $td_style.">".$card[ 'mana' ]; ?></td>
					<td <?php echo $td_style."><a class='card_link' href='" . $image_url . "'>".$card[ 'name' ]; ?></a></td>
					<td <?php echo $td_style.">".$card[ 'normal' ]; ?></td>
					<td <?php echo $td_style.">".$card[ 'gold' ]; ?></td>
					<td <?php echo $td_style.">".$card[ 'card_class' ]; ?></td>
					<td <?php echo $td_style.">".$card[ 'card_set' ]; ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>

<!-- Updating -->
<div style="float: left;">
	<table border="0" cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=2><b>Update Card</b></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Card:</td>
				<td>
					<div class="styled-select slate">
						<select id="card_options">
							<?php
								foreach ( $cards as $key => $card )
								{
									echo "<option card_normal=".$card['normal']." card_gold=".$card['gold']." value=".$card[ 'id' ].">".$card[ 'name' ]."</option>";
								}
							?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>Normal:</td>
				<td><input type="text" value="<?php echo $cards[ 0 ][ 'normal' ]; ?>" id="update_card_normal"></td>
			</tr>
			<tr>
				<td>Gold:</td>
				<td><input type="text" value="<?php echo $cards[ 0 ][ 'gold' ]; ?>" id="update_card_gold"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" value="Update" id="update_card_update" /></td>
			</tr>
		</tbody>
	</table>

	<table border="0" cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan="2"><b>Update Account</b></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Current Dust:</td>
				<td><input type="text" id="account_update_cur_dust" value="<?php echo $account[ 'dust_am' ]; ?>" /></td>
			</tr>
			<tr>
				<td>D/E Total:</td>
				<td><input type="text" id="account_update_de_total" value="<?php echo $account[ 'dust_de' ]; ?>" /></td>
			</tr>
			<tr>
				<td>Gold Total:</td>
				<td><input type="text" id="account_update_gold_total" value="<?php echo $account[ 'gold' ]; ?>" /></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" id="account_update_save" value="Update" /></td>
			</tr>
		</tbody>
	</table>

	<table border="0" cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan="2"><b>Add Opened Pack</b></th colspan="2">
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Common:</td>
				<td><input type="text" id="pack_add_common" value="0" /></td>
			</tr>
			<tr>
				<td>Golden Common:</td>
				<td><input type="text" id="pack_add_c_gold" value="0" /></td>
			</tr>
			<tr>
				<td>Rare:</td>
				<td><input type="text" id="pack_add_rare" value="0" /></td>
			</tr>
			<tr>
				<td>Golden Rare:</td>
				<td><input type="text" id="pack_add_r_gold" value="0" /></td>
			</tr>
			<tr>
				<td>Epic:</td>
				<td><input type="text" id="pack_add_epic" value="0" /></td>
			</tr>
			<tr>
				<td>Golden Epic:</td>
				<td><input type="text" id="pack_add_e_gold" value="0" /></td>
			</tr>
			<tr>
				<td>Legendary:</td>
				<td><input type="text" id="pack_add_legend" value="0" /></td>
			</tr>
			<tr>
				<td>Golden Legendary:</td>
				<td><input type="text" id="pack_add_l_gold" value="0" /></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="button" id="pack_add" value="Add" /></td>
			</tr>
		</tbody>
	</table>
</div>

<!-- Missing card stats -->
<div style="float: left;">
	<?php if ( $card_stats[ 'available' ] > 0 && $missing == 'None' ): ?>
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=5><?php echo 'Card Stats for Class '.$class; ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
				<td>Dust To Complete</td>
			</tr>
			<tr>
				<td><?php echo $card_stats[ 'playable' ]; ?></td>
				<td><?php echo $card_stats[ 'available' ]; ?></td>
				<td><?php echo $card_stats[ 'remaining' ]; ?></td>
				<td><?php echo round( ( $card_stats[ 'playable' ] / $card_stats[ 'available' ] ) * 100, 2 ); ?>%</td>
				<td><?php echo number_format( $card_stats[ 'dust_comp' ] ); ?></td>
			</tr>
		</tbody>
	</table>
	<?php endif; ?>

	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=6>Number of Cards By Set</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Set</td>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
				<td>Dust To Complete</td>
			</tr>
			<?php
				$total_playable 	= 0;
				$total_available 	= 0;
				$total_remaining 	= 0;
				$total_dust_comp 	= 0;

				foreach ( $card_sets as $key => $set )
				{
					Functions::Cards_Load_Set( $database2, $set[ 'name' ], $set_cards );
					Functions::Cards_Convert_Stats( $set_cards, $set_stats );

					$total_playable 	= $total_playable + $set_stats[ 'playable' ];
					$total_available 	= $total_available + $set_stats[ 'available' ];
					$total_remaining 	= $total_remaining + $set_stats[ 'remaining' ];
					$total_dust_comp 	+= $set_stats[ 'dust_comp' ];

					echo "<tr>
							<td>".$set[ 'name' ]."</td>
							<td>".$set_stats[ 'playable' ]."</td>
							<td>".$set_stats[ 'available' ]."</td>
							<td>".$set_stats[ 'remaining' ]."</td>
							<td>".number_format( 100 * ( $set_stats[ 'playable' ] / $set_stats[ 'available' ] ), 2 )."%</td>
							<td>".number_format( $set_stats[ 'dust_comp' ] )."</td>
						  </tr>";
				}
			?>
			<tr>
				<td>Total</td>
				<td><?php echo $total_playable; ?></td>
				<td><?php echo $total_available; ?></td>
				<td><?php echo $total_remaining; ?></td>
				<td><?php echo number_format( 100 * ( $total_playable / $total_available ), 2 )."%"; ?></td>
				<td><?php echo number_format( $total_dust_comp ); ?></td>
			</tr>
		</tbody>
	</table>

	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=5>Number of Cards By Rarity</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Rarity</td>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
			</tr>
			<tr>
				<td>Basic</td>
				<td><?php echo $basic_stats[ 'playable' ]; ?></td>
				<td><?php echo $basic_stats[ 'available' ]; ?></td>
				<td><?php echo $basic_stats[ 'remaining' ]; ?></td>
				<td><?php echo number_format( 100 * ( $basic_stats[ 'playable' ] / $basic_stats[ 'available' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td>Common</td>
				<td><?php echo $common_stats[ 'playable' ]; ?></td>
				<td><?php echo $common_stats[ 'available' ]; ?></td>
				<td><?php echo $common_stats[ 'remaining' ]; ?></td>
				<td><?php echo number_format( 100 * ( $common_stats[ 'playable' ] / $common_stats[ 'available' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td>Rare</td>
				<td><?php echo $rare_stats[ 'playable' ]; ?></td>
				<td><?php echo $rare_stats[ 'available' ]; ?></td>
				<td><?php echo $rare_stats[ 'remaining' ]; ?></td>
				<td><?php echo number_format( 100 * ( $rare_stats[ 'playable' ] / $rare_stats[ 'available' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td>Epic</td>
				<td><?php echo $epic_stats[ 'playable' ]; ?></td>
				<td><?php echo $epic_stats[ 'available' ]; ?></td>
				<td><?php echo $epic_stats[ 'remaining' ]; ?></td>
				<td><?php echo number_format( 100 * ( $epic_stats[ 'playable' ] / $epic_stats[ 'available' ] ), 2 )."%"; ?></td>
			</tr>
			<tr>
				<td>Legendary</td>
				<td><?php echo $legendary_stats[ 'playable' ]; ?></td>
				<td><?php echo $legendary_stats[ 'available' ]; ?></td>
				<td><?php echo $legendary_stats[ 'remaining' ]; ?></td>
				<td><?php echo number_format( 100 * ( $legendary_stats[ 'playable' ] / $legendary_stats[ 'available' ] ), 2 )."%"; ?></td>
			</tr>
		</tbody>
	</table>
</div>

<!-- Specific Stats -->
<div style="float: left;">
	<!-- Basic -->
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=6>Basic Card Stats</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Set</td>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
				<td>Dust to Complete</td>
			</tr>
			<?php
				foreach ( $card_sets as $key => $set )
				{
					if ( $set[ 'name' ] != 'Classic' )	continue;

					Functions::CardList_Load_SetAndRarity( $database2, $set[ 'name' ], 'Basic', $cards );
					Functions::Cards_Convert_BasicStats( $cards, $card_stats );

					echo "<tr>
							<td>" . $set[ 'name' ] . "</td>
							<td>" . $card_stats[ 'playable' ] . "</td>
							<td>" . $card_stats[ 'available' ] . "</td>
							<td>" . $card_stats[ 'remaining' ] . "</td>
							<td>" . $card_stats[ 'percent_comp' ] . "%</td>
							<td>" . number_format( $card_stats[ 'dust_comp' ] ) . "</td>
						  </tr>";
				}
			?>
		</tbody>
	</table>

	<!-- Common -->
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=6>Common Card Stats</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Set</td>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
				<td>Dust to Complete</td>
			</tr>
			<?php
				foreach ( $card_sets as $key => $set )
				{
					Functions::CardList_Load_SetAndRarity( $database2, $set[ 'name' ], 'Common', $cards );
					Functions::Cards_Convert_CommonStats( $cards, $card_stats );

					echo "<tr>
							<td>" . $set[ 'name' ] . "</td>
							<td>" . $card_stats[ 'playable' ] . "</td>
							<td>" . $card_stats[ 'available' ] . "</td>
							<td>" . $card_stats[ 'remaining' ] . "</td>
							<td>" . $card_stats[ 'percent_comp' ] . "%</td>
							<td>" . number_format( $card_stats[ 'dust_comp' ] ) . "</td>
						  </tr>";
				}
			?>
		</tbody>
	</table>

	<!-- Rare -->
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=6>Rare Card Stats</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Set</td>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
				<td>Dust to Complete</td>
			</tr>
			<?php
				foreach ( $card_sets as $key => $set )
				{
					Functions::CardList_Load_SetAndRarity( $database2, $set[ 'name' ], 'Rare', $cards );
					Functions::Cards_Convert_RareStats( $cards, $card_stats );

					echo "<tr>
							<td>" . $set[ 'name' ] . "</td>
							<td>" . $card_stats[ 'playable' ] . "</td>
							<td>" . $card_stats[ 'available' ] . "</td>
							<td>" . $card_stats[ 'remaining' ] . "</td>
							<td>" . $card_stats[ 'percent_comp' ] . "%</td>
							<td>" . number_format( $card_stats[ 'dust_comp' ] ) . "</td>
						  </tr>";
				}
			?>
		</tbody>
	</table>

	<!-- Epic -->
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=6>Epic Card Stats</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Set</td>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
				<td>Dust to Complete</td>
			</tr>
			<?php
				foreach ( $card_sets as $key => $set )
				{
					if ( $set[ 'name' ] == 'BRM' )	continue;

					Functions::CardList_Load_SetAndRarity( $database2, $set[ 'name' ], 'Epic', $cards );
					Functions::Cards_Convert_EpicStats( $cards, $card_stats );

					echo "<tr>
							<td>" . $set[ 'name' ] . "</td>
							<td>" . $card_stats[ 'playable' ] . "</td>
							<td>" . $card_stats[ 'available' ] . "</td>
							<td>" . $card_stats[ 'remaining' ] . "</td>
							<td>" . $card_stats[ 'percent_comp' ] . "%</td>
							<td>" . number_format( $card_stats[ 'dust_comp' ] ) . "</td>
						  </tr>";
				}
			?>
		</tbody>
	</table>

	<!-- Legendary -->
	<table cellpadding="2" cellspacing="0" style="width: auto; margin-right: 20px;">
		<thead>
			<tr>
				<th colspan=6>Legendary Card Stats</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Set</td>
				<td>Playable</td>
				<td>Available</td>
				<td>Remaining</td>
				<td>% Complete</td>
				<td>Dust to Complete</td>
			</tr>
			<?php
				foreach ( $card_sets as $key => $set )
				{
					if ( $set[ 'name' ] == 'BRM' )	continue;

					Functions::CardList_Load_SetAndRarity( $database2, $set[ 'name' ], 'Legendary', $cards );
					Functions::Cards_Convert_LegendaryStats( $cards, $card_stats );

					echo "<tr>
							<td>" . $set[ 'name' ] . "</td>
							<td>" . $card_stats[ 'playable' ] . "</td>
							<td>" . $card_stats[ 'available' ] . "</td>
							<td>" . $card_stats[ 'remaining' ] . "</td>
							<td>" . $card_stats[ 'percent_comp' ] . "%</td>
							<td>" . number_format( $card_stats[ 'dust_comp' ] ) . "</td>
						  </tr>";
				}
			?>
		</tbody>
	</table>
</div>
<?php
$layout->footer();

/*
//INSERTING NEW CARDS
$lines 	= file( "cards_insert_missing_TGT.txt" );
foreach ( $lines as $line_num => $line )
{
	$card = array();
    //Set	Class	Mana	Name	Rarity	Normal	Gold
    list( $card[ 'card_set' ], $card[ 'card_class' ], $card[ 'mana' ], $card[ 'name' ], $card[ 'rarity' ], $card[ 'normal' ], $card[ 'gold' ] ) = split("\t", $line);

	Functions::Card_Insert( $database2, $card );

}
*/
?>