<?php
require_once( 'classes/database.php' );

class Functions
{
	private $database2;

	public function __construct()
	{
		$this->database2	= new Database2();
	}

	public static function Get( $value )
	{
		return isset( $_GET[ $value ] ) ? trim( $_GET[ $value ] ) : '';
	}

	public static function Get_Int( $value )
	{
		return isset( $_GET[ $value ] ) ? (int)$_GET[ $value ] : 0;
	}

	public static function Post( $value )
	{
		return isset( $_POST[ $value ] ) ? trim( $_POST[ $value ] ) : '';
	}

	public static function Post_Int( $value )
	{
		return isset( $_POST[ $value ] ) ? (int)$_POST[ $value ] : 0;
	}

	public static function Post_Float( $value )
	{
		return isset( $_POST[ $value ] ) ? (float)$_POST[ $value ] : 0.00;
	}

	public static function Post_Active( $value )
	{
		if ( isset( $_POST[ $value ] ) && ( $_POST[ $value ] == 'true' || $_POST[ $value ] == 1 ) )
		{
			return 1;
		}

		return 0;
	}

	public static function Filename( $filename )
	{
		if ( !preg_match( "/^[a-zA-Z_]+$/", $filename ) )
		{
			return false;
		}

		return true;
	}

	public static function Error( $code, $message )
	{
		global $error_code;
		global $error_message;

		$error_code 	= $code;
		$error_message 	= $message;

		return false;
	}

	public static function Card_Insert( &$db, $card )
	{
		return $db->query( 'INSERT INTO cards
							( mana, name, rarity, normal, gold, card_set, card_class )
							VALUES
							( ?, ?, ?, ?, ?, ?, ? )',
							$card[ 'mana' ], $card[ 'name' ], $card[ 'rarity' ], $card[ 'normal' ], $card[ 'gold' ], $card[ 'card_set' ], $card[ 'card_class' ] );
	}

	public static function Card_Update( &$db, $card )
	{
		return $db->query( 'UPDATE cards
							SET
								normal 	= ?,
								gold	= ?
							WHERE
								id 		= ?',
							$card[ 'normal' ], $card[ 'gold' ], $card[ 'id' ] );
	}

	public static function Cards_Load_Class( &$db, $card_class, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_class = ?
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name', $cards, $card_class );
	}

	public static function Cards_Load_Rarity( &$db, $rarity, &$cards )
	{
		return $db->select( 'SELECT * FROM cards WHERE rarity = ?', $cards, $rarity );
	}

	public static function Cards_Load_Set( &$db, $card_set, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_set = ?
							 ORDER BY
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name', $cards, $card_set );
	}

	public static function CardList_Load_SetAndRarity( &$db, $card_set, $rarity, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_set 	= ? AND
							 	rarity 		= ?',
							$cards, $card_set, $rarity );
	}

	public static function CardList_Load_All( &$db, &$cards )
	{
		return $db->select( 'SELECT * FROM cards ORDER BY rarity', $cards );
	}

	public static function Card_Load_Id( &$db, $card_id, &$card )
	{
		return $db->single( 'SELECT * FROM cards WHERE id = ?', $card, $card_id );
	}

	public static function CardList_Load_Missing( &$db, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	( rarity <> \'Legendary\' AND ( normal + gold ) < 2 ) OR
							 	( rarity = \'Legendary\' AND ( normal + gold ) < 1 )
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name', $cards );
	}

	public static function CardList_Load_Class_MissingPlayable( &$db, $class, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_class = ? AND
							 	( ( rarity <> \'Legendary\' AND ( normal + gold ) < 2 ) OR ( rarity = \'Legendary\' AND ( normal + gold ) < 1 ) )
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name',
							$cards, $class );
	}

	public static function CardList_Load_Class_MissingNormal( &$db, $class, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_class = ? AND
							 	( ( rarity <> \'Legendary\' AND normal < 2 ) OR ( rarity = \'Legendary\' AND normal < 1 ) )
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name',
							$cards, $class );
	}

	public static function CardList_Load_Class_MissingGolden( &$db, $class, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_class = ? AND
							 	( ( rarity <> \'Legendary\' AND gold < 2 ) OR ( rarity = \'Legendary\' AND gold < 1 ) )
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name',
							$cards, $class );
	}

	public static function CardList_Load_Rarity_MissingPlayable( &$db, $rarity, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	rarity = ? AND
							 	( ( rarity <> \'Legendary\' AND ( normal + gold ) < 2 ) OR ( rarity = \'Legendary\' AND ( normal + gold ) < 1 ) )
							 ORDER by
							 	CASE card_class
							 		WHEN "Neutral" 	THEN 1
							 		WHEN "Druid" 	THEN 2
							 		WHEN "Hunter" 	THEN 3
							 		WHEN "Mage" 	THEN 4
							 		WHEN "Paladin" 	THEN 5
							 		WHEN "Priest" 	THEN 6
							 		WHEN "Rogue" 	THEN 7
							 		WHEN "Shaman" 	THEN 8
							 		WHEN "Rogue" 	THEN 9
							 		WHEN "Warrior" 	THEN 10
							 		ELSE 11
							 	END, mana, name',
							$cards, $rarity );
	}

	public static function CardList_Load_Rarity_MissingNormal( &$db, $rarity, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	rarity = ? AND
							 	( ( rarity <> \'Legendary\' AND normal < 2 ) OR ( rarity = \'Legendary\' AND normal < 1 ) )
							 ORDER by
							 	CASE card_class
							 		WHEN "Neutral" 	THEN 1
							 		WHEN "Druid" 	THEN 2
							 		WHEN "Hunter" 	THEN 3
							 		WHEN "Mage" 	THEN 4
							 		WHEN "Paladin" 	THEN 5
							 		WHEN "Priest" 	THEN 6
							 		WHEN "Rogue" 	THEN 7
							 		WHEN "Shaman" 	THEN 8
							 		WHEN "Rogue" 	THEN 9
							 		WHEN "Warrior" 	THEN 10
							 		ELSE 11
							 	END, mana, name',
							$cards, $rarity );
	}

	public static function CardList_Load_Rarity_MissingGolden( &$db, $rarity, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	rarity = ? AND
							 	( ( rarity <> \'Legendary\' AND gold < 2 ) OR ( rarity = \'Legendary\' AND gold < 1 ) )
							 ORDER by
							 	CASE card_class
							 		WHEN "Neutral" 	THEN 1
							 		WHEN "Druid" 	THEN 2
							 		WHEN "Hunter" 	THEN 3
							 		WHEN "Mage" 	THEN 4
							 		WHEN "Paladin" 	THEN 5
							 		WHEN "Priest" 	THEN 6
							 		WHEN "Rogue" 	THEN 7
							 		WHEN "Shaman" 	THEN 8
							 		WHEN "Rogue" 	THEN 9
							 		WHEN "Warrior" 	THEN 10
							 		ELSE 11
							 	END, mana, name',
							$cards, $rarity );
	}

	public static function CardList_Load_Set_MissingPlayable( &$db, $card_set, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_set = ? AND
							 	( ( rarity <> \'Legendary\' AND ( normal + gold ) < 2 ) OR ( rarity = \'Legendary\' AND ( normal + gold ) < 1 ) )
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name',
							$cards, $card_set );
	}

	public static function CardList_Load_Set_MissingNormal( &$db, $card_set, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_set = ? AND
							 	( ( rarity <> \'Legendary\' AND normal < 2 ) OR ( rarity = \'Legendary\' AND normal < 1 ) )
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name',
							$cards, $card_set );
	}

	public static function CardList_Load_Set_MissingGolden( &$db, $card_set, &$cards )
	{
		return $db->select( 'SELECT
								*
							 FROM
							 	cards
							 WHERE
							 	card_set = ? AND
							 	( ( rarity <> \'Legendary\' AND gold < 2 ) OR ( rarity = \'Legendary\' AND gold < 1 ) )
							 ORDER by
							 	CASE rarity
							 		WHEN "Basic" 		THEN 1
							 		WHEN "Common" 		THEN 2
							 		WHEN "Rare" 		THEN 3
							 		WHEN "Epic" 		THEN 4
							 		WHEN "Legendary" 	THEN 5
							 		ELSE 6
							 	END, mana, name',
							$cards, $card_set );
	}

	public static function Cards_Convert_All_Stats( $cards, &$stats )
	{
		$stats = array();
		$stats[ 'common_available' ] 	= 0;
		$stats[ 'common_missing' ] 		= 0;
		$stats[ 'common_gold_missing' ] = 0;

		$stats[ 'rare_available' ] 		= 0;
		$stats[ 'rare_missing' ] 		= 0;
		$stats[ 'rare_gold_missing' ] 	= 0;

		$stats[ 'epic_available' ] 		= 0;
		$stats[ 'epic_missing' ] 		= 0;
		$stats[ 'epic_gold_missing' ] 	= 0;

		$stats[ 'legend_available' ] 	= 0;
		$stats[ 'legend_missing' ] 		= 0;
		$stats[ 'legend_gold_missing' ] = 0;

		$stats[ 'gold_de_total' ] 		= 0;
		$stats[ 'normal_de_total' ] 	= 0;
		$stats[ 'golden_dust_needed' ] 	= 0;

		foreach ( $cards as $key => $card )
		{
			//Available
			if ( $card[ 'rarity' ] == 'Legendary' )		++$stats[ 'legend_available' ];
			else if ( $card[ 'rarity' ] == 'Epic' )		$stats[ 'epic_available' ]		+= 2;
			else if ( $card[ 'rarity' ] == 'Rare' )		$stats[ 'rare_available' ]		+= 2;
			else if ( $card[ 'rarity' ] == 'Common' )	$stats[ 'common_available' ]	+= 2;

			// Missing
			if ( ( $card[ 'rarity' ] == 'Legendary' ) && ( ( $card[ 'normal' ] + $card[ 'gold' ] ) < 1 ) )
			{
				++$stats[ 'legend_missing' ];
			}
			else if ( $card[ 'rarity' ] != 'Legendary' && ( ( $card[ 'normal' ] + $card[ 'gold' ] ) < 2 ) )
			{
				$remaining = 2 - ( $card[ 'normal' ] + $card[ 'gold' ] );

				if ( $card[ 'rarity' ] == 'Common' )	$stats[ 'common_missing' ] 	+= $remaining;
				else if ( $card[ 'rarity' ] == 'Rare' )	$stats[ 'rare_missing' ] 	+= $remaining;
				else if ( $card[ 'rarity' ] == 'Epic' )	$stats[ 'epic_missing' ] 	+= $remaining;
			}

			// Gold Missing
			if ( $card[ 'rarity' ] == 'Legendary' )		$stats[ 'legend_gold_missing' ] += 1 - $card[ 'gold' ];
			else if ( $card[ 'rarity' ] == 'Epic' )		$stats[ 'epic_gold_missing' ] 	+= 2 - $card[ 'gold' ];
			else if ( $card[ 'rarity' ] == 'Rare' )		$stats[ 'rare_gold_missing' ] 	+= 2 - $card[ 'gold' ];
			else if ( $card[ 'rarity' ] == 'Common' )	$stats[ 'common_gold_missing' ] += 2 - $card[ 'gold' ];

			// Golden DE Total
			if ( $card[ 'rarity' ] == 'Legendary' && ( $card[ 'normal' ] + $card[ 'gold' ] > 1 ) )		$stats[ 'gold_de_total' ] += 1600 * $card[ 'gold' ];
			else if ( $card[ 'rarity' ] == 'Epic' && ( $card[ 'normal' ] + $card[ 'gold' ] > 2 ) )		$stats[ 'gold_de_total' ] += 400 * $card[ 'gold' ];
			else if ( $card[ 'rarity' ] == 'Rare' && ( $card[ 'normal' ] + $card[ 'gold' ] > 2 ) )		$stats[ 'gold_de_total' ] += 100 * $card[ 'gold' ];
			else if ( $card[ 'rarity' ] == 'Common' && ( $card[ 'normal' ] + $card[ 'gold' ] > 2 ) )	$stats[ 'gold_de_total' ] += 50 * $card[ 'gold' ];

			// Normal DE Total
			if ( $card[ 'card_set' ] != 'Naxx' && $card[ 'card_set' ] != 'BRM' )
			{
				if ( $card[ 'rarity' ] == 'Legendary' )		$stats[ 'normal_de_total' ] += 400 * $card[ 'normal' ];
				else if ( $card[ 'rarity' ] == 'Epic' )		$stats[ 'normal_de_total' ] += 100 * $card[ 'normal' ];
				else if ( $card[ 'rarity' ] == 'Rare' )		$stats[ 'normal_de_total' ] += 20 * $card[ 'normal' ];
				else if ( $card[ 'rarity' ] == 'Common' )	$stats[ 'normal_de_total' ] += 5 * $card[ 'normal' ];
			}

			// Golden Dust Needed
			if ( ( $card[ 'rarity' ] == 'Legendary' ) && $card[ 'gold' ] == 0 )
			{
				$stats[ 'golden_dust_needed' ] += 3200;
			}
			else if ( $card[ 'rarity' ] != 'Legendary' )
			{
				$remaining = 2 - $card[ 'gold' ];

				if ( $card[ 'rarity' ] == 'Common' )	$stats[ 'golden_dust_needed' ] 	+= $remaining * 400;
				else if ( $card[ 'rarity' ] == 'Rare' )	$stats[ 'golden_dust_needed' ] 	+= $remaining * 800;
				else if ( $card[ 'rarity' ] == 'Epic' )	$stats[ 'golden_dust_needed' ] 	+= $remaining * 1600;
			}
		}
	}

	public static function Cards_Convert_Rarity_Stats( $basic, $common, $rare, $epic, $legendary, &$dust_stats )
	{
		$dust_stats	= array();
		$dust_stats[ 'basic_dust' ] 	= 0;
		$dust_stats[ 'common_dust' ]	= 0;
		$dust_stats[ 'rare_dust' ]		= 0;
		$dust_stats[ 'epic_dust' ]		= 0;
		$dust_stats[ 'legend_dust' ]	= 0;

		foreach ( $common as $key => $card )
		{
			$missing = ( $card[ 'normal' ] + $card[ 'gold' ] ) > 2 ? 2 : ( $card[ 'normal' ] + $card[ 'gold' ] );

			$dust_stats[ 'common_dust' ] += ( 2 - $missing ) * 40;
		}

		foreach ( $rare as $key => $card )
		{
			$missing = ( $card[ 'normal' ] + $card[ 'gold' ] ) > 2 ? 2 : ( $card[ 'normal' ] + $card[ 'gold' ] );

			$dust_stats[ 'rare_dust' ] += ( 2 - $missing ) * 100;
		}

		foreach ( $epic as $key => $card )
		{
			$missing = ( $card[ 'normal' ] + $card[ 'gold' ] ) > 2 ? 2 : ( $card[ 'normal' ] + $card[ 'gold' ] );

			$dust_stats[ 'epic_dust' ] += ( 2 - $missing ) * 400;
		}

		foreach ( $legendary as $key => $card )
		{
			$missing = ( $card[ 'normal' ] + $card[ 'gold' ] ) > 1 ? 1 : ( $card[ 'normal' ] + $card[ 'gold' ] );

			$dust_stats[ 'legend_dust' ] += ( 1 - $missing ) * 1600;
		}
	}

	public static function Cards_Convert_Stats( $cards, &$card_stats )
	{
		$card_stats 				= array();
		$card_stats[ 'playable' ]	= 0;
		$card_stats[ 'available' ]	= 0;
		$card_stats[ 'remaining' ] 	= 0;
		$card_stats[ 'dust_comp' ] 	= 0;

		foreach ( $cards as $key => $card )
		{
			if ( $card[ 'rarity' ] == 'Legendary' )
			{
				++$card_stats[ 'available' ];

				if ( ( $card[ 'normal' ] + $card[ 'gold' ] ) >= 1 )
				{
					++$card_stats[ 'playable' ];
				}
				else
				{
					++$card_stats[ 'remaining' ];
					$card_stats[ 'dust_comp' ] += 1600;
				}
			}
			else
			{
				$card_stats[ 'available' ] = $card_stats[ 'available' ] + 2;

				if ( ( $card[ 'normal' ] + $card[ 'gold' ] ) >= 2 )
				{
					$card_stats[ 'playable' ] 	+= 2;
				}
				else
				{
					$card_stats[ 'remaining' ] 	+= 2 - ( $card[ 'normal' ] + $card[ 'gold' ] );
					$card_stats[ 'playable' ] 	+= $card[ 'normal' ] + $card[ 'gold' ];

					if ( $card[ 'rarity' ] == 'Common' )	$card_stats[ 'dust_comp' ] += 40 * ( 2 - ( $card[ 'normal' ] + $card[ 'gold' ] ) );
					else if ( $card[ 'rarity' ] == 'Rare' )	$card_stats[ 'dust_comp' ] += 100 * ( 2 - ( $card[ 'normal' ] + $card[ 'gold' ] ) );
					else if ( $card[ 'rarity' ] == 'Epic' )	$card_stats[ 'dust_comp' ] += 400 * ( 2 - ( $card[ 'normal' ] + $card[ 'gold' ] ) );
				}
			}
		}
	}

	public static function Cards_Convert_BasicStats( $cards, &$card_stats )
	{
		$card_stats 					= array();
		$card_stats[ 'playable' ]		= 0;
		$card_stats[ 'available' ]		= 0;
		$card_stats[ 'remaining' ] 		= 0;
		$card_stats[ 'percent_comp' ] 	= 0;
		$card_stats[ 'dust_comp' ] 		= 0;

		foreach ( $cards as $key => $card )
		{
			$card_stats[ 'available' ] = $card_stats[ 'available' ] + 2;

			if ( ( $card[ 'normal' ] + $card[ 'gold' ] ) >= 2 )
			{
				$card_stats[ 'playable' ] 	+= 2;
			}
			else
			{
				$card_stats[ 'remaining' ] 	+= 2 - ( $card[ 'normal' ] + $card[ 'gold' ] );
				$card_stats[ 'playable' ] 	+= $card[ 'normal' ] + $card[ 'gold' ];
			}
		}

		$card_stats[ 'percent_comp' ] 	= number_format( $card_stats[ 'playable' ] / $card_stats[ 'available' ], 2 ) * 100;
		$card_stats[ 'dust_comp' ]		= $card_stats[ 'remaining' ] * 0;
	}

	public static function Cards_Convert_CommonStats( $cards, &$card_stats )
	{
		$card_stats 					= array();
		$card_stats[ 'playable' ]		= 0;
		$card_stats[ 'available' ]		= 0;
		$card_stats[ 'remaining' ] 		= 0;
		$card_stats[ 'percent_comp' ] 	= 0;
		$card_stats[ 'dust_comp' ] 		= 0;

		foreach ( $cards as $key => $card )
		{
			$card_stats[ 'available' ] = $card_stats[ 'available' ] + 2;

			if ( ( $card[ 'normal' ] + $card[ 'gold' ] ) >= 2 )
			{
				$card_stats[ 'playable' ] 	+= 2;
			}
			else
			{
				$card_stats[ 'remaining' ] 	+= 2 - ( $card[ 'normal' ] + $card[ 'gold' ] );
				$card_stats[ 'playable' ] 	+= $card[ 'normal' ] + $card[ 'gold' ];
			}
		}

		$card_stats[ 'percent_comp' ] 	= number_format( $card_stats[ 'playable' ] / $card_stats[ 'available' ], 2 ) * 100;
		$card_stats[ 'dust_comp' ]		= $card_stats[ 'remaining' ] * 40;
	}

	public static function Cards_Convert_RareStats( $cards, &$card_stats )
	{
		$card_stats 					= array();
		$card_stats[ 'playable' ]		= 0;
		$card_stats[ 'available' ]		= 0;
		$card_stats[ 'remaining' ] 		= 0;
		$card_stats[ 'percent_comp' ] 	= 0;
		$card_stats[ 'dust_comp' ] 		= 0;

		foreach ( $cards as $key => $card )
		{
			$card_stats[ 'available' ] = $card_stats[ 'available' ] + 2;

			if ( ( $card[ 'normal' ] + $card[ 'gold' ] ) >= 2 )
			{
				$card_stats[ 'playable' ] 	+= 2;
			}
			else
			{
				$card_stats[ 'remaining' ] 	+= 2 - ( $card[ 'normal' ] + $card[ 'gold' ] );
				$card_stats[ 'playable' ] 	+= $card[ 'normal' ] + $card[ 'gold' ];
			}
		}

		$card_stats[ 'percent_comp' ] 	= number_format( $card_stats[ 'playable' ] / $card_stats[ 'available' ], 2 ) * 100;
		$card_stats[ 'dust_comp' ]		= $card_stats[ 'remaining' ] * 100;
	}

	public static function Cards_Convert_EpicStats( $cards, &$card_stats )
	{
		$card_stats 					= array();
		$card_stats[ 'playable' ]		= 0;
		$card_stats[ 'available' ]		= 0;
		$card_stats[ 'remaining' ] 		= 0;
		$card_stats[ 'percent_comp' ] 	= 0;
		$card_stats[ 'dust_comp' ] 		= 0;

		foreach ( $cards as $key => $card )
		{
			$card_stats[ 'available' ] = $card_stats[ 'available' ] + 2;

			if ( ( $card[ 'normal' ] + $card[ 'gold' ] ) >= 2 )
			{
				$card_stats[ 'playable' ] 	+= 2;
			}
			else
			{
				$card_stats[ 'remaining' ] 	+= 2 - ( $card[ 'normal' ] + $card[ 'gold' ] );
				$card_stats[ 'playable' ] 	+= $card[ 'normal' ] + $card[ 'gold' ];
			}
		}

		$card_stats[ 'percent_comp' ] 	= number_format( $card_stats[ 'playable' ] / $card_stats[ 'available' ], 2 ) * 100;
		$card_stats[ 'dust_comp' ]		= $card_stats[ 'remaining' ] * 400;
	}

	public static function Cards_Convert_LegendaryStats( $cards, &$card_stats )
	{
		$card_stats 					= array();
		$card_stats[ 'playable' ]		= 0;
		$card_stats[ 'available' ]		= 0;
		$card_stats[ 'remaining' ] 		= 0;
		$card_stats[ 'percent_comp' ] 	= 0;
		$card_stats[ 'dust_comp' ] 		= 0;

		foreach ( $cards as $key => $card )
		{
			++$card_stats[ 'available' ];

			if ( ( $card[ 'normal' ] + $card[ 'gold' ] ) >= 1 )	++$card_stats[ 'playable' ];
			else												++$card_stats[ 'remaining' ];	
		}

		$card_stats[ 'percent_comp' ] 	= number_format( $card_stats[ 'playable' ] / $card_stats[ 'available' ], 2 ) * 100;
		$card_stats[ 'dust_comp' ]		= $card_stats[ 'remaining' ] * 1600;
	}

	/*
	*
	* Helper functions for table packs
	*
	*/
	public static function Pack_Update( &$db, $pack )
	{
		return $db->query( 'UPDATE packs
							SET
								opened	= ?,
								common	= ?,
								c_gold	= ?,
								rare	= ?,
								r_gold	= ?,
								epic	= ?,
								e_gold	= ?,
								legend	= ?,
								l_gold	= ?',
							$pack[ 'opened' ], $pack[ 'common' ], $pack[ 'c_gold' ], $pack[ 'rare' ], $pack[ 'r_gold' ],
							$pack[ 'epic' ], $pack[ 'e_gold' ], $pack[ 'legend' ], $pack[ 'l_gold' ] );
	}

	public static function Pack_Load( &$db, &$packs )
	{
		return $db->single( "SELECT * FROM packs", $packs );
	}

	public static function Pack_Convert_Stats( $stats, $packs, $account, &$pack_stats )
	{
		$pack_stats = array();
		$pack_stats[ 'common_percent' ] 		= $packs[ 'common' ] 	/ ( $packs[ 'opened' ] * 5 );
		$pack_stats[ 'c_gold_percent' ] 		= $packs[ 'c_gold' ] 	/ ( $packs[ 'opened' ] * 5 );
		$pack_stats[ 'rare_percent' ] 			= $packs[ 'rare' ] 		/ ( $packs[ 'opened' ] * 5 );
		$pack_stats[ 'r_gold_percent' ] 		= $packs[ 'r_gold' ] 	/ ( $packs[ 'opened' ] * 5 );
		$pack_stats[ 'epic_percent' ] 			= $packs[ 'epic' ]	 	/ ( $packs[ 'opened' ] * 5 );
		$pack_stats[ 'e_gold_percent' ] 		= $packs[ 'e_gold' ] 	/ ( $packs[ 'opened' ] * 5 );
		$pack_stats[ 'legend_percent' ] 		= $packs[ 'legend' ] 	/ ( $packs[ 'opened' ] * 5 );
		$pack_stats[ 'l_gold_percent' ] 		= $packs[ 'l_gold' ] 	/ ( $packs[ 'opened' ] * 5 );

		$pack_stats[ 'average_dust' ] 			= ( 5 		* ( $packs[ 'common' ] 	/ $packs[ 'opened' ] ) ) +
												  ( 50		* ( $packs[ 'c_gold' ] 	/ $packs[ 'opened' ] ) ) +
												  ( 20 		* ( $packs[ 'rare' ] 	/ $packs[ 'opened' ] ) ) +
												  ( 100 	* ( $packs[ 'r_gold' ] 	/ $packs[ 'opened' ] ) ) +
												  ( 100 	* ( $packs[ 'epic' ]	/ $packs[ 'opened' ] ) ) +
												  ( 400 	* ( $packs[ 'e_gold' ] 	/ $packs[ 'opened' ] ) ) +
												  ( 400 	* ( $packs[ 'legend' ] 	/ $packs[ 'opened' ] ) ) +
												  ( 1600 	* ( $packs[ 'l_gold' ] 	/ $packs[ 'opened' ] ) );

		$pack_stats[ 'dust_miss_card' ]			= ( ( 40 *  ( $stats[ 'common_missing' ] 	/ $stats[ 'common_available' ] 	) ) * ( ( $packs[ 'common' ] 	+ $packs[ 'c_gold' ] )	/ $packs[ 'opened' ] ) ) +
												  ( ( 100 * ( $stats[ 'rare_missing' ] 	 	/ $stats[ 'rare_available' ]	) ) * ( ( $packs[ 'rare' ] 		+ $packs[ 'r_gold' ] ) 	/ $packs[ 'opened' ] ) ) +
												  ( ( 400 * ( $stats[ 'epic_missing' ] 	 	/ $stats[ 'epic_available' ]	) ) * ( ( $packs[ 'epic' ] 		+ $packs[ 'e_gold' ] )	/ $packs[ 'opened' ] ) ) +
												  ( ( 1600 * ( $stats[ 'legend_missing' ] 	/ $stats[ 'legend_available' ]	) ) * ( ( $packs[ 'legend' ] 	+ $packs[ 'l_gold' ] ) 	/ $packs[ 'opened' ] ) );

		$pack_stats[ 'golddust_miss_card' ]		= ( ( 400 *  ( $stats[ 'common_gold_missing' ] 	/ $stats[ 'common_available' ] 	) ) * ( $packs[ 'c_gold' ]	/ $packs[ 'opened' ] ) ) +
												  ( ( 800 * ( $stats[ 'rare_gold_missing' ] 	/ $stats[ 'rare_available' ]	) ) * ( $packs[ 'r_gold' ] 	/ $packs[ 'opened' ] ) ) +
												  ( ( 1600 * ( $stats[ 'epic_gold_missing' ] 	/ $stats[ 'epic_available' ]	) ) * ( $packs[ 'e_gold' ]	/ $packs[ 'opened' ] ) ) +
												  ( ( 3200 * ( $stats[ 'legend_gold_missing' ] 	/ $stats[ 'legend_available' ]	) ) * ( $packs[ 'l_gold' ] 	/ $packs[ 'opened' ] ) );

		$pack_stats[ 'packs_to_buy' ]			= ( $stats[ 'total_dust' ] - $account[ 'dust_am' ] - $account[ 'dust_de' ] ) / $pack_stats[ 'average_dust' ];
		$pack_stats[ 'packs_to_buy_miss' ]		= ( $stats[ 'total_dust' ] - $account[ 'dust_am' ] - $account[ 'dust_de' ] ) / ( $pack_stats[ 'average_dust' ] + $pack_stats[ 'dust_miss_card' ] );
		$pack_stats[ 'packs_to_buy_gold_de' ]	= ( $stats[ 'total_dust' ] - $account[ 'dust_am' ] - $account[ 'dust_de' ] - $stats[ 'gold_de_total' ] ) / ( $pack_stats[ 'average_dust' ] + $pack_stats[ 'dust_miss_card' ] );
		$pack_stats[ 'packs_to_buy_golden' ]	= ( $stats[ 'golden_dust_needed' ] - $account[ 'dust_am' ] - $account[ 'dust_de' ] - $stats[ 'normal_de_total' ] ) / ( $pack_stats[ 'average_dust' ] + $pack_stats[ 'golddust_miss_card' ] );
	}

	/*
	*
	* Helper functions for table account
	*
	*/
	public static function Account_Update( &$db, $account )
	{
		return $db->query( 'UPDATE account
							SET
								dust_am	= ?,
								dust_de	= ?,
								gold	= ?,
								last_e	= ?,
								last_l  = ?',
							$account[ 'dust_am' ], $account[ 'dust_de' ], $account[ 'gold' ], $account[ 'last_e' ], $account[ 'last_l' ] );
	}

	public static function Account_Load( &$db, &$account )
	{
		return $db->single( "SELECT * FROM account", $account );
	}

	/*
	*
	* Helper functions for table classes
	*
	*/
	public static function ClassList_Load( &$db, &$classes )
	{
		return $db->select( 'SELECT * FROM classes ORDER BY id', $classes );
	}

	public static function Class_Load_Name( &$db, $name, &$class )
	{
		return $db->single( "SELECT * FROM classes WHERE name = ?", $class, $name );
	}

	public static function ClassList_Load_Classes( &$db, &$classes )
	{
		return $db->select( "SELECT * FROM classes WHERE non_class = 0 ORDER BY id", $classes );
	}

	/*
	*
	* Helper functions for table sets
	*
	*/
	public static function SetList_Load( &$db, &$sets )
	{
		return $db->select( 'SELECT * FROM sets ORDER BY id', $sets );
	}

	public static function Set_Load_Name( &$db, $name, &$set )
	{
		return $db->single( 'SELECT * FROM sets WHERE name = ?', $set, $name );
	}

	/*
	*
	* Helper Functions for table rarity
	*
	*/
	public static function RarityList_Load_All( &$db, &$rarities )
	{
		return $db->select( 'SELECT * FROM rarity ORDER BY id', $rarities );
	}

	public static function Rarity_Load_Name( &$db, $name, &$rarity )
	{
		return $db->single( 'SELECT * FROM rarity WHERE name = ?', $rarity, $name );
	}

	/*
	*
	* Helper Functions for table arena_run
	*
	*/
	public static function ArenaRun_Insert( &$db, $arena_run )
	{
		return $db->query( 'INSERT INTO arena_run
							( active, arena_class, notes, timestamp )
							VALUES
							( ?, ?, ?, ? )',
							$arena_run[ 'active' ], $arena_run[ 'arena_class' ], $arena_run[ 'notes' ], $arena_run[ 'timestamp' ] );
	}

	public static function ArenaRun_Update( &$db, $arena_run )
	{
		return $db->query( 'UPDATE arena_run
							SET
								notes 	= ? AND
								active 	= ?
							WHERE
								id 		= ?',
							$arena_run[ 'notes' ], $arena_run[ 'active' ],
							$arena_run[ 'id' ] );
	}

	public static function ArenaRun_Load_Active( &$db, &$arena_run )
	{
		return $db->single( 'SELECT * FROM arena_run WHERE active = 1', $arena_run );
	}

	public static function ArenaRun_Load_ID( &$db, $id, &$arena_run )
	{
		return $db->single( 'SELECT * FROM arena_run WHERE id = ?', $arena_run, $id );
	}

	public static function ArenaRun_Delete( &$db, $id )
	{
		Functions::ArenaMatch_Delete_ArenaID( $db, $id );
		Functions::ArenaRewards_Delete_ArenaID( $db, $id );

		return $db->query( 'DELETE FROM arena_run WHERE id = ?', $id );
	}

	/*
	*
	* Helper Functions for table arena_matches
	*
	*/
	public static function ArenaMatch_Insert( &$db, $arena_match )
	{
		return $db->query( 'INSERT INTO arena_matches
							( arena_id, opponent, coin, win )
							VALUES
							( ?, ?, ?, ? )',
							$arena_match[ 'arena_id' ], $arena_match[ 'opponent' ], $arena_match[ 'coin' ], $arena_match[ 'win' ] );
	}

	public static function ArenaMatchList_Load_ArenaID( &$db, $arena_id, &$arena_matches )
	{
		return $db->select( 'SELECT * FROM arena_matches WHERE arena_id = ?', $arena_matches, $arena_id );
	}

	public static function ArenaMatch_Delete_ArenaID( &$db, $arena_id )
	{
		return $db->query( 'DELETE FROM arena_matches WHERE arena_id = ?', $arena_id );
	}

	public static function ArenaMatches_LossMax( &$db, $arena_id )
	{
		$db->select( 'SELECT COUNT( win ) FROM arena_matches WHERE win = 0 AND arena_id = ?', $count, $arena_id );

		if ( $count >= 3 )
		{
			return false;
		}

		return true;
	}

	public static function ArenaMatch_Validate( &$db, $arena_match )
	{
		if ( !Functions::Class_Load_Name( $db, $arena_match[ 'opponent' ], $class ) )
		{
			return false;
		}

		if ( $arena_match[ 'coin' ] != 0 AND $arena_match[ 'coin' ] != 1 )
		{
			Functions::Error( '#Error#', 'Invalid coin value' );
			return false;
		}

		if ( $arena_match[ 'win' ] != 0 AND $arena_match[ 'win' ] != 1 )
		{
			Functions::Error( '#Error#', 'Invalid win value' );
			return false;
		}

		if ( $arena_match[ 'win' ] == 0 )
		{
			if ( Functions::ArenaMatches_LossMax( $db, $arena_match[ 'arena_id' ] ) )
			{
				//Retire
				//Trigger rewards
				Functions::Error( '#Error#', 'This arena run already has 3 losses' );
				return false;
			}
		}
	}

	/*
	*
	* Helper Functions for table arena_rewards
	*
	*/
	public static function ArenaRewards_Insert( &$db, $arena_rewards )
	{
		return $db->query( 'INSERT INTO arena_rewards
							( arena_id, gold, packs, dust, normal_cards, gold_cards )
							VALUES
							( ?, ?, ?, ?, ?, ? )',
							$arena_rewards[ 'arena_id' ], $arena_rewards[ 'gold' ], $arena_rewards[ 'packs' ],
							$arena_rewards[ 'dust' ], $arena_rewards[ 'normal_cards' ], $arena_rewards[ 'gold_cards' ] );
	}

	public static function ArenaRewards_Update( &$db, $arena_rewards )
	{
		return $db->query( 'UPDATE arena_rewards
							SET
								gold 			= ?,
								packs 			= ?,
								dust 			= ?,
								normal_cards 	= ?,
								gold_cards		= ?
							WHERE
								arena_id 		= ?', 
							$arena_rewards[ 'gold' ], $arena_rewards[ 'packs' ], $arena_rewards[ 'dust' ], $arena_rewards[ 'normal_cards' ], $arena_rewards[ 'gold_cards' ],
							$arena_rewards[ 'arena_id' ] );
	}

	public static function ArenaRewards_Load_ArenaID( &$db, $arena_id, &$arena_rewards )
	{
		return $db->single( 'SELECT * FROM arena_rewards WHERE arena_id = ?', $arena_rewards, $arena_id );
	}

	public static function ArenaRewards_Delete_ArenaID( &$db, $arena_id )
	{
		return $db->query( 'DELETE FROM arena_rewards WHERE arena_id = ?', $arena_id );
	}



/*
DB HELER
	Select multiple
		return $db->select( 'SELECT * FROM cards WHERE class = \'neutral\' ORDER by mana, name', $neutral_cards );

	SELECT single
		return $db->single( 'SELECT p.bill_rate, p.jr_rate, p.prog_rate, rp.custom_rate FROM projects p, report_projects rp WHERE rp.report_id = ? AND p.id = ? AND p.id = rp.project_id', $rates, $report_id, $project_id );

	query
		return $db->query( 'INSERT INTO projects
							( id, name, bill_rate, jr_rate, prog_rate, cust_login, pays_monthly )
							VALUES
							( ?, ?, ?, ?, ?, ?, ? )',
							$project[ 'id' ], $project[ 'name' ], $project[ 'bill_rate' ], $project[ 'jr_rate' ], $project[ 'prog_rate' ], $project[ 'cust_login' ], $project[ 'pays_monthly' ] );

	Update
		return $db->query( 'UPDATE
								projects
							SET
								name			= ?,
								bill_rate		= ?,
								jr_rate			= ?,
								prog_rate		= ?,
								cust_login		= ?,
								pays_monthly	= ?
							WHERE
								id				= ?',
							$project[ 'name' ], $project[ 'bill_rate' ], $project[ 'jr_rate' ], $project[ 'prog_rate' ], $project[ 'cust_login' ], $project[ 'pays_monthly' ], $project[ 'id' ] );
*/
}
?>