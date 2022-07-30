<?php 

// sessie voor kaarten in het spel
// sessie voor de dealer of bank
// sessie voor speler

//aangepaste spelregels:
//1. Speler kan niet splitten
//2. Speler kan geen geld inzetten
//3. soft 17 regel is van toepassing

session_start(); 

//kaarten uitleg --> letters H= harten S=schoppen D=ruit C=Klaver T=10 J=boer Q=queen K=king A=aas.
$kaarten_alles = array('2C' => 2, '3C' => 3, '4C' => 4, '5C' => 5, '6C' => 6, '7C' => 7, '8C' => 8, '9C' => 9, 'TC' => 10, 'JC' => 10, 'QC' => 10, 'KC' => 10, 'AC' => 11, 
    '2H' => 2, '3H' => 3, '4H' => 4, '5H' => 5, '6H' => 6, '7H' => 7, '8H' => 8, '9H' => 9, 'TH' => 10, 'JH' => 10, 'QH' => 10, 'KH' => 10, 'AH' => 11, 
    '2S' => 2, '3S' => 3, '4S' => 4, '5S' => 5, '6S' => 6, '7S' => 7, '8S' => 8, '9S' => 9, 'TS' => 10, 'JS' => 10, 'QS' => 10, 'KS' => 10, 'AS' => 11,
    '2D' => 2, '3D' => 3, '4D' => 4, '5D' => 5, '6D' => 6, '7D' => 7, '8D' => 8, '9D' => 9, 'TD' => 10, 'JD' => 10, 'QD' => 10, 'KD' => 10 , 'AD' => 11  );

$winnaar_txt = '';
$pas_txt = ''; 
$punten_bank_txt = null;
$disabled_reset = '';
$disabled_pas = '';
$disabled_kaart = '';
$winnaar = false; 

function kaartenSchudden($my_array = array())
{
    $KaartGeschut = array();
    while (count($my_array)) { 
		$element = array_rand($my_array);//neemt een rand-array-elementen bij zijn sleutel
        $KaartGeschut[$element] = $my_array[$element]; // wijs de array en zijn waarde toe aan een andere array
        unset($my_array[$element]);//verwijder het element uit de bronarray
    }
    return $KaartGeschut;
}

function KaartTrekken($slof) 
{
    global $speelkaarten; 
    //print_r($slof);
    $getrokkenkaart =  array_key_last($slof);
    array_splice($slof, -1);
    
    $speelkaarten = $slof;
    $_SESSION['KaartenInHetSpel'] = $slof;
	
    return $getrokkenkaart;
}

function print_kaarten($print_kaarten, $wie)
{
	global $winnaar;
	$KaartOpScherm = ''; 
	if ( $wie == 'bank' AND $winnaar == false)
	{
		$KaartOpScherm = '<img src="cards/closed.png" class="kaarten"/>'  . '<img src="cards/' . $print_kaarten[1] . '.png" class="kaarten"/>';
	}
	else
	{
		$teller = count($print_kaarten);
		for ($i = 0; $i < $teller; $i++) {
			$KaartOpScherm  = $KaartOpScherm  . '<img src="cards/' . $print_kaarten[$i] . '.png" class="kaarten"/>';
		}
	}
	
	return $KaartOpScherm ;
}

function punten_tellen($x, $kaarten_alles) 
{
   	global $winnaar_txt; 
	global $disabled_reset;
	global $disabled_kaart;
	global $disabled_pas;
	global $punten_bank_txt;
	Global $punten_bank;
	$punten = 0;
	$aas = 0;
	$teller = count($x);

		for ($i = 0; $i < $teller; $i++) {
			if ($kaarten_alles[$x[$i]] != 11) { // check de kaart is geen aas
				$punten = $punten + $kaarten_alles[$x[$i]];
				
			}
			else {
                $aas++;
            }
        }

    

    for ($iii = 0; $iii < $aas; $iii++) { //Een aas kan de waarde 1 of 11 hebben.

       if ($punten+11 == 21 )
	   {
          $punten = $punten + 11;
	   }
	   elseif ($punten + 11 < 21)
	   {
		$punten = $punten + 11;
	   }
	   else
	   {
		$punten = $punten + 1;
	   }

	}
	return $punten; 
}

function winnaar($p_bank, $p_speler, $sp_keuze)
{
	//echo $p_bank   . '/' . $p_speler;
	global $disabled_kaart;
	global $disabled_pas;
	global $disabled_reset;
	global $winnaar_txt;
	global $print_stand;
	global $einde_spel;
    global $winnaar;
	global $speelkaarten;
	global $kaartenVanBank;
	global $kaarten_alles;
	$winnaar = true;
    if ( $p_bank == 21 AND $p_speler == 21 AND $sp_keuze == 'kaart')
	{
         $temp_b = count($_SESSIE['KaartenVanBank']);
		// $temp_s = count($_SESSIE['KaartenVanSpeler']);

		 if($temp_b == 2  )
		 {
			$disabled_kaart = 'disabled';
			$disabled_pas = 'disabled';
			$disabled_reset = '';
			$winnaar_txt = 'Bank has BLACKJACK!! The bank wins!!';
		    $print_stand =  'Points bank:: ' . $p_bank . ' | Points player: ' . $p_speler;
		 }
		 else
		 {
			$disabled_kaart = 'disabled';
			$disabled_pas = 'disabled';
			$disabled_reset = '';
			$winnaar_txt = 'Tie';
		   $print_stand =  'Points bank:: ' . $p_bank . ' | Points player: ' . $p_speler;
		 }

		  
	}
	elseif ($p_bank < 21 AND $p_speler == 21 AND $sp_keuze == 'kaart')
	{

		while ($p_bank < 17) { 
				
			$nieuwekaartvoorbank = KaartTrekken($speelkaarten); //hiermee krijgt de bank een nieuwe kaart 
			array_push($kaartenVanBank, $nieuwekaartvoorbank);
			$_SESSION['kaartenVanBank'] = $kaartenVanBank;
			$p_bank = punten_tellen($_SESSION['kaartenVanBank'], $kaarten_alles);
		} 


		if ($p_bank < 21 AND $p_speler == 21 AND $sp_keuze == 'kaart') {

			//Bank(aantal punten 18-20) heeft minder punten dan Speler (21 punten) -- Speler wint
				$disabled_kaart = 'disabled';
				$disabled_pas = 'disabled';
				$disabled_reset = '';
		 		$winnaar_txt = 'Player wins';
		 		$print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler; 

			
		}   elseif ($p_bank == 21 AND $p_speler == 21 AND $sp_keuze == 'kaart') {

			//Bank en Speler gelijk aantal punten -- gelijk spel 	 
				$disabled_kaart = 'disabled';
				$disabled_pas = 'disabled';
				$disabled_reset = '';
				$winnaar_txt = 'Tie';
			   $print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler;
		
				 
		}  	elseif ($p_bank > 21 AND $p_speler == 21 AND $sp_keuze == 'kaart') {

			// Bank koopt zich dood -- speler wint 
				$print_stand =  'Points bank:: ' . $p_bank . ' | Points player: ' . $p_speler; 
				$winnaar_txt = 'Player wins!!!';
				$disabled_kaart = 'disabled';
				$disabled_pas = 'disabled';
				$disabled_reset = '';
		}
	}
	elseif ($p_bank < 21 AND $p_speler > 21 AND $sp_keuze == 'kaart' )
	{
		// speler koopt zich dood
		$print_stand =  'Points bank: ' . $p_bank . ' | Points playerr: ' . $p_speler; 
        $winnaar_txt = 'Bank wins';
		$disabled_kaart = 'disabled';
		$disabled_pas = 'disabled';
		$disabled_reset = '';
		
	}
	elseif ( $p_bank == 21 AND $p_speler < 21 AND $sp_keuze == 'pas' )
	{
         // bank wint
		$print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler; 
        $winnaar_txt = 'Bank wins!!!';
		$disabled_kaart = 'disabled';
		$disabled_pas = 'disabled';
		$disabled_reset = '';
	}
	elseif ( $p_bank < 21 AND $p_speler < 21 AND $sp_keuze == 'pas' )
	{
		if ($p_bank > $p_speler)
		{
             // bank wint
			$print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler; 
			$winnaar_txt = 'Bank wins!!!';
			$disabled_kaart = 'disabled';
			$disabled_pas = 'disabled';
			$disabled_reset = '';
		}
		elseif($p_bank < $p_speler)
		{
             // Speler wint
			$print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler; 
			$winnaar_txt = 'Player wins!!!';
			$disabled_kaart = 'disabled';
			$disabled_pas = 'disabled';
			$disabled_reset = '';
		}
		
		else
		{
             // gelijk
			$print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler; 
			$winnaar_txt = 'Tie';
			$disabled_kaart = 'disabled';
			$disabled_pas = 'disabled';
			$disabled_reset = '';
		}
			
	}
	elseif ( $p_bank > 21 AND $p_speler < 21 AND $sp_keuze == 'pas' )
		{
			// speler wint
			$print_stand =  'Points bank:' . $p_bank . ' | Points player: ' . $p_speler; 
			$winnaar_txt = 'Player wins!!!';
			$disabled_kaart = 'disabled';
			$disabled_pas = 'disabled';
			$disabled_reset = '';
		}

		elseif (  $p_speler == 21 AND $sp_keuze == 'niks' )
		{
            while ($p_bank < 17) {
				
				$nieuwekaartvoorbank = KaartTrekken($speelkaarten); ///hiermee krijgt de bank een niuwe kaart 
				//echo 'Als dit ziet, dan zit je in regel 164  ';///?????????????
				array_push($kaartenVanBank, $nieuwekaartvoorbank);
				$_SESSION['kaartenVanBank'] = $kaartenVanBank;
				$p_bank = punten_tellen($_SESSION['kaartenVanBank'], $kaarten_alles);
			} 

			if ( count($_SESSION['kaartenVanBank']) == 2 AND $p_bank == 21)
			{

				 // gelijk
				 $print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler; 
				 $winnaar_txt = 'Tie';
				 $disabled_kaart = 'disabled';
				 $disabled_pas = 'disabled';
				 $disabled_reset = '';
			}
			else
			{
                // speler wint
				$print_stand =  'Points bank: ' . $p_bank . ' | Points player: ' . $p_speler; 
				$winnaar_txt = 'Player wins!!!';
				$disabled_kaart = 'disabled';
				$disabled_pas = 'disabled';
				$disabled_reset = '';
			}
			
		}
	else
	{
		$winnaar_txt = 'Select an option';
		$print_stand = 'Points player: ' . $p_speler; 
		$disabled_reset = 'disabled';
		$winnaar = false;
	}
}

function keuzeMenu($kaarten_alles) 


{  
	global $kaartenVanBank;
	global $speelkaarten; 
	global $punten_speler;
	global $punten_bank;
    if (isset($_POST ['keuze'])) {
        if ($_POST ['keuze'] == 'pas') {  
			$punten_bank = punten_tellen($_SESSION['kaartenVanBank'], $kaarten_alles);
			$punten_speler = punten_tellen($_SESSION['kaartenVanSpeler'], $kaarten_alles);

			while ($punten_bank < 17) {
				
				$nieuwekaartvoorbank = KaartTrekken($speelkaarten); ///hiermee krijgt de bank een niuwe kaart 
				//echo 'Als dit ziet, dan zit je in regel 164  ';///?????????????
				array_push($kaartenVanBank, $nieuwekaartvoorbank);
				$_SESSION['kaartenVanBank'] = $kaartenVanBank;
				$punten_bank = punten_tellen($_SESSION['kaartenVanBank'], $kaarten_alles);
			} 
			winnaar($punten_bank, $punten_speler, 'pas');
			
        } 
		elseif ($_POST ['keuze'] == 'kaart') {
			//eerst kaart trekken
			global $kaartenVanSpeler;
			array_push ( $kaartenVanSpeler , KaartTrekken($_SESSION['KaartenInHetSpel'] ));
			$_SESSION['kaartenVanSpeler'] = $kaartenVanSpeler;
			$punten_bank = punten_tellen($_SESSION['kaartenVanBank'], $kaarten_alles);
			$punten_speler = punten_tellen($_SESSION['kaartenVanSpeler'], $kaarten_alles);
			winnaar($punten_bank, $punten_speler, 'kaart');


        }
    }
}


if (isset($_POST['keuze'])) {
	if ($_POST ['keuze'] == 'reset') {
		session_destroy();
		session_start();
	}
} else {
	session_destroy();
	session_start();
}

if (isset($_POST['keuze'])) { 

	if ($_POST ['keuze'] != 'reset') {
			$kaarten = $_SESSION['KaartenInHetSpel'];   
			
    } else {
        $kaarten = $kaarten_alles; 
    }
} else {
    $kaarten = $kaarten_alles;
}

$speelkaarten = array();
$speelkaarten = kaartenSchudden($kaarten); 

if (!isset($_SESSION['kaartenVanSpeler'])) {
	$kaartenVanSpeler = array();
	$kaartenVanBank = array();

	// Speler begint met 2 kaarten
    for ($i = 0; $i < 2; $i++) { 

        array_push($kaartenVanSpeler, KaartTrekken($speelkaarten)); //push is toevoegen van kaarten aan array door functie kaarttrekken
		
    }

    // De bank begint ook met 2 kaarten
    for ($i = 0; $i < 2; $i++) { 
        array_push($kaartenVanBank, KaartTrekken($speelkaarten));
    }
	
   // altijd punten tellen eerste keer
	$_SESSION['kaartenVanSpeler'] = $kaartenVanSpeler;
	$_SESSION['kaartenVanBank'] = $kaartenVanBank;
	
	$punten_bank = punten_tellen($_SESSION['kaartenVanBank'], $kaarten_alles);
	$punten_speler = punten_tellen($_SESSION['kaartenVanSpeler'], $kaarten_alles);

	winnaar($punten_bank, $punten_speler , 'niks');
	
} else {
	
	$kaartenVanSpeler = $_SESSION['kaartenVanSpeler'];
	$kaartenVanBank = $_SESSION['kaartenVanBank'];
	//$punten_bank = punten_tellen($_SESSION['kaartenVanBank'], $kaarten_alles);
	//$punten_speler = punten_tellen($_SESSION['kaartenVanSpeler'], $kaarten_alles);
}

array_push($kaartenVanSpeler, keuzeMenu($kaarten_alles)); //hier start het aanroepen van kaart voor speler

//echo '|'. count($speelkaarten) . '|';
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Blackjack</title>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Akshar&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
    	    <style>
        	    body {
            	    color: white;
	                font-weight:bold;
    	            background-image: url("images/chesterfield_green.png");
					font-family: 'Akshar', sans-serif;
            	}
	            form {
    	        	display:table-row; 
 	            	align: center;
   	            	width: 50%; 
					font-size:25px;
	 	        }
				.kaarten{
					margin:5px;
					filter: drop-shadow(5px 5px 5px #333);
					
				}
				.button
				{
					font-weight:bold;
					border: solid 3px lightgreen;
					background-color: transparent;
					color: white;
					border-radius:8px;
                    font-family: 'Akshar', sans-serif;
					font-size: 21px;
				}
				.button:hover
				{
					font-weight:bold;
					border: solid 3px darkgreen;
					background-color: lightgreen;
					color: darkgreen;
					border-radius:8px;
                    font-family: 'Akshar', sans-serif;
					font-size: 21px;
					transition-duration: 0.5s;
				}
        </style>
    </head>
	<body> 
		<center>
			 <img src="images/logokl.png" alt="logo" style="width:400px;position:relative;top:20px;" />
		</center>

		<center>
			<h2>Bank
				<br>
					<?php echo print_kaarten($_SESSION['kaartenVanBank'], 'bank'); ?>
				<br>
			</h2>
		</center>
		<br>
		<center>
			<h2>Player
			<br>
				<?php echo print_kaarten($_SESSION['kaartenVanSpeler'], 'speler'); ?>
					<hr>
						<?php echo $print_stand; ?>
					<hr>
			</h2>
		</center>
		<br>
		<center>
			<form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" >
  			<input type="radio" name="keuze" value="kaart" <?php echo $disabled_kaart; ?> required>Card
  			<input type="radio" name="keuze"  value="pas" <?php echo $disabled_pas; ?> required>Pass
  			<input type="radio" name="keuze"  value="reset" <?php echo $disabled_reset; ?> required>Start over
  			<input type="submit" name="submit" class="button" value=" GO! ">  
			</form>
			
			<?php echo '<p style="font-size:2em;">' . $winnaar_txt . '</p>'; ?>
			<?php echo '<p style="font-size:2em;">' . $pas_txt . '</p>'; ?>
		</center>
	</body>
</html>