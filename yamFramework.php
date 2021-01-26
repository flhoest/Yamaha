<?php

/*
			██╗   ██╗ █████╗ ███╗   ███╗ █████╗ ██╗  ██╗ █████╗ 
			╚██╗ ██╔╝██╔══██╗████╗ ████║██╔══██╗██║  ██║██╔══██╗
			 ╚████╔╝ ███████║██╔████╔██║███████║███████║███████║
			  ╚██╔╝  ██╔══██║██║╚██╔╝██║██╔══██║██╔══██║██╔══██║
			   ██║   ██║  ██║██║ ╚═╝ ██║██║  ██║██║  ██║██║  ██║
			   ╚═╝   ╚═╝  ╚═╝╚═╝     ╚═╝╚═╝  ╚═╝╚═╝  ╚═╝╚═╝  ╚═╝

	Php function collection for Yamaha amplifiers.
	(c) 2021 F.Lhoest - https://www.lets-talk-about.tech/

	Send command to Amps inspiration script : 
			https://forum.jeedom.com/viewtopic.php?t=17977&start=40
	Yamaha official API reference guide : 
		Basic : https://wiki.elvis.science/images/9/9c/Yamaha_Extended_Control_API_Specification_%28Basic%29.pdf
		Advanced : https://wiki.elvis.science/images/5/5c/Yamaha_Extended_Control_API_Specification_%28Advanced%29.pdf

	My Net Radio preset list : 

	// 1 : Tipik
	// 2 : La Premiere
	// 3 : Radio Contact
	// 4 : CHERIE FM
	// 5 : Mint
	// 6 : Nostalgie
	// 7 : VivaCite Namur
	// 8 : XMas Radio

	// Function index in alphabetical order (total 13)
	//------------------------------------------------

	// yamCustVol($ip,$vol)
	// yamCustVolZ2($ip,$vol)
	// yamStartMain($ip)
	// yamStartZ2_NetRadio($ip)
	// yamStartZ2_Sel1($ip)
	// yamStartZ2_Sel2($ip)
	// yamStart_Sel1($ip)
	// yamStop($ip)
	// yamStopZ2($ip)
	// yamVolDownZ2($ip)
	// yamVolUpZ2($ip)
	// yamZ2Mute($ip)
	// yamZ2_45($ip)

*/

	// --------------
	// Init section
	// --------------

	// Array of commands and their instructions
	$commands=array(
				// Main Zone
				"POWER_ON" => "<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Power_Control><Power>On</Power></Power_Control></Main_Zone></YAMAHA_AV>",
				"POWER_OFF"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Power_Control><Power>Standby</Power></Power_Control></Main_Zone></YAMAHA_AV>",
				"VOLUME_MUTE"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Volume><Mute>On/Off</Mute></Volume></Main_Zone></YAMAHA_AV>",
				"VOLUME_UP"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Volume><Lvl><Val>Up 5 dB</Val><Exp></Exp><Unit></Unit></Lvl></Volume></Main_Zone></YAMAHA_AV>",
				"VOLUME_DOWN"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Volume><Lvl><Val>Down 5 dB</Val><Exp></Exp><Unit></Unit></Lvl></Volume></Main_Zone></YAMAHA_AV>",
				"INP_AV5"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Input><Input_Sel>AV5</Input_Sel></Input></Main_Zone></YAMAHA_AV>",
				"INP_NETRADIO"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Input><Input_Sel>NET RADIO</Input_Sel></Input></Main_Zone></YAMAHA_AV>",
				"INP_HDMI1"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Input><Input_Sel>HDMI1</Input_Sel></Input></Main_Zone></YAMAHA_AV>",
				"INP_HDMI2"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Input><Input_Sel>HDMI2</Input_Sel></Input></Main_Zone></YAMAHA_AV>",
				"INP_HDMI3"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Input><Input_Sel>HDMI3</Input_Sel></Input></Main_Zone></YAMAHA_AV>",
				"STATUS"=>"<YAMAHA_AV cmd=\"GET\"><Main_Zone><Basic_Status>GetParam</Basic_Status></Main_Zone></YAMAHA_AV>",
				"VOL_45"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Volume><Lvl><Val>-450</Val><Exp>1</Exp><Unit>dB</Unit></Lvl></Volume></Main_Zone></YAMAHA_AV>",
				"VOL_55"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Volume><Lvl><Val>-550</Val><Exp>1</Exp><Unit>dB</Unit></Lvl></Volume></Main_Zone></YAMAHA_AV>",
				"VOL_65"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Volume><Lvl><Val>-650</Val><Exp>1</Exp><Unit>dB</Unit></Lvl></Volume></Main_Zone></YAMAHA_AV>",
				"VOL_CUST"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><Volume><Lvl><Val>!!VALUE!!</Val><Exp>1</Exp><Unit>dB</Unit></Lvl></Volume></Main_Zone></YAMAHA_AV>",

				// Zone 2
				"Z2_POWER_ON"=>"<YAMAHA_AV cmd=\"PUT\"><Zone_2><Power_Control><Power>On</Power></Power_Control></Zone_2></YAMAHA_AV>",
				"Z2_POWER_OFF"=>"<YAMAHA_AV cmd=\"PUT\"><Zone_2><Power_Control><Power>Standby</Power></Power_Control></Zone_2></YAMAHA_AV>",
				"Z2_INP_NETRADIO"=>"<YAMAHA_AV cmd=\"PUT\"><Zone_2><Input><Input_Sel>NET RADIO</Input_Sel></Input></Zone_2></YAMAHA_AV>",
				"Z2_VOL_45"=>"<YAMAHA_AV cmd=\"PUT\"><Zone_2><Volume><Lvl><Val>-450</Val><Exp>1</Exp><Unit>dB</Unit></Lvl></Volume></Zone_2></YAMAHA_AV>",
				"Z2_VOL_CUST"=>"<YAMAHA_AV cmd=\"PUT\"><Zone_2><Volume><Lvl><Val>!!VALUE!!</Val><Exp>1</Exp><Unit>dB</Unit></Lvl></Volume></Zone_2></YAMAHA_AV>",
				"Z2_VOL_UP2" => "<YAMAHA_AV cmd=\"PUT\"><Zone_2><Volume><Lvl><Val>Up 2 dB</Val><Exp></Exp><Unit></Unit></Lvl></Volume></Zone_2></YAMAHA_AV>",
				"Z2_VOL_DOWN2" => "<YAMAHA_AV cmd=\"PUT\"><Zone_2><Volume><Lvl><Val>Down 2 dB</Val><Exp></Exp><Unit></Unit></Lvl></Volume></Zone_2></YAMAHA_AV>",
				"Z2_MUTE_TOG" => "<YAMAHA_AV cmd=\"PUT\"><Zone_2><Volume><Mute>On/Off</Mute></Volume></Zone_2></YAMAHA_AV>",

				// Not Zone related
				"SEL1"=>"<YAMAHA_AV cmd=\"PUT\"><NET_RADIO><List_Control><Direct_Sel>Line_1</Direct_Sel></List_Control></NET_RADIO></YAMAHA_AV>",
				"SEL2"=>"<YAMAHA_AV cmd=\"PUT\"><NET_RADIO><List_Control><Direct_Sel>Line_2</Direct_Sel></List_Control></NET_RADIO></YAMAHA_AV>",
				"SEL3"=>"<YAMAHA_AV cmd=\"PUT\"><NET_RADIO><List_Control><Direct_Sel>Line_3</Direct_Sel></List_Control></NET_RADIO></YAMAHA_AV>",
				"RETURN"=>"<YAMAHA_AV cmd=\"PUT\"><Main_Zone><List_Control><Cursor>Return</Cursor></List_Control></Main_Zone></YAMAHA_AV>"
				);

	// ----------------------------------------
	// Function sending http command to the amp
	// ----------------------------------------

    function sendToAmp($host,$method,$path='/',$data='')
    {
		$buf="";
        $method = strtoupper($method);
        $fp = fsockopen($host, 80) or die("Unable to open socket");

        fputs($fp, "$method $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Content-type: text/plain\r\n");

        if ($method == 'POST') fputs($fp, "Content-length: " . strlen($data) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");

        if ($method == 'POST') fputs($fp, $data);

        while (!feof($fp))
          $buf .= fgets($fp,256);

        fclose($fp);
        return $buf;
    }

	// ----------------------------------
	// Function Customer volume Main Zone
	// ----------------------------------

	function yamCustVol($ip,$vol)
	{
		global $commands;
	
		$command=$commands["VOL_CUST"];
		$command=str_replace("!!VALUE!!", $vol, $command);
		// 	Power ON Main
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$command);
	}

	// ----------------------------------
	// Function Customer volume Main Zone
	// ----------------------------------

	function yamCustVolZ2($ip,$vol)
	{
		global $commands;
	
		$command=$commands["Z2_VOL_CUST"];
		$command=str_replace("!!VALUE!!", $vol, $command);
		// 	Power ON Main
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$command);
	}

	// ---------------------------
	// Function starting Main Zone
	// ---------------------------

	function yamStartMain($ip)
	{
		global $commands;
	
		// 	Power ON Main
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["POWER_ON"]);
		sleep(1);

	}

	// -------------------------------------------------------------------------------
	// Function starting Main Zone, Net Radio input and first preset radio in the list
	// -------------------------------------------------------------------------------

	function yamStart_Sel1($ip)
	{
		global $commands;
	
		// 	Power ON Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["POWER_ON"]);
		sleep(1);

		// Set volume to -45
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["VOL_45"]);

		// Switch to Net Radio
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["INP_NETRADIO"]);

		// Top Selection list
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["RETURN"]);
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["RETURN"]);
		
		// Selection "Favoris"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
		sleep(1);
	
		// Selection "My__Favorites"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
		sleep(1);

		// Selection "Tipik"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
		sleep(1);
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
	}

	// --------------------------------
	// Function switching off Main Zone
	// --------------------------------

	function yamStop($ip)
	{
		global $commands;
	
		// 	Power OFF Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["POWER_OFF"]);
	}

	// -----------------------------------------------
	// Function starting Zone 2, input Net Radio input
	// -----------------------------------------------

	function yamStartZ2_NetRadio($ip)
	{
		global $commands;
	
		// 	Power ON Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_POWER_ON"]);
		sleep(1);

		// Set volume to -45
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_VOL_45"]);

		// Z2 switch to Net Radio
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_INP_NETRADIO"]);
	}

	// ----------------------------------------------------------------------------
	// Function starting Zone 2, Net Radio input and first preset radio in the list
	// ----------------------------------------------------------------------------

	function yamStartZ2_Sel1($ip)
	{
		global $commands;
	
		// 	Power ON Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_POWER_ON"]);
		sleep(1);

		// Set volume to -45
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_VOL_45"]);

		// Z2 switch to Net Radio
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_INP_NETRADIO"]);

		// Top Selection list
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["RETURN"]);
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["RETURN"]);
		
		// Z2 selection "Favoris"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
		sleep(1);
	
		// Z2 selection "My__Favorites"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
		sleep(1);

		// Z2 selection "La Premiere"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
	}

	// -----------------------------------------------------------------------------
	// Function starting Zone 2, Net Radio input and Second preset radio in the list
	// -----------------------------------------------------------------------------

	function yamStartZ2_Sel2($ip)
	{
		global $commands;
	
		// 	Power ON Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_POWER_ON"]);
		sleep(1);

		// Set volume to -45
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_VOL_45"]);

		// Z2 switch to Net Radio
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_INP_NETRADIO"]);

		// Top Selection list
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["RETURN"]);
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["RETURN"]);
		
		// Z2 selection "Favoris"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
		sleep(1);
	
		// Z2 selection "My__Favorites"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL1"]);
		sleep(1);

		// Z2 selection "La Premiere"
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["SEL2"]);
	}

	// -----------------------------
	// Function switching off Zone 2
	// -----------------------------

	function yamStopZ2($ip)
	{
		global $commands;
	
		// 	Power OFF Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_POWER_OFF"]);
	}

	// -------------------------------------
	// Function increading vol on z2 by 2 dB
	// -------------------------------------

	function yamVolUpZ2($ip)
	{
		global $commands;
	
		// 	Volume up 2 dB Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_VOL_UP2"]);
	}

	// -------------------------------------
	// Function decreading vol on z2 by 2 dB
	// -------------------------------------

	function yamVolDownZ2($ip)
	{
		global $commands;
	
		// 	Volume down 2 dB Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_VOL_DOWN2"]);
	}

	// -----------------------
	// Function Z2 Mute toggle
	// -----------------------

	function yamZ2Mute($ip)
	{
		global $commands;
	
		// 	Mute Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_MUTE_TOG"]);
	}
	
	// -------------------------
	// Function Z2 volume -45 dB
	// -------------------------

	function yamZ2_45($ip)
	{
		global $commands;
	
		// 	Mute Z2
		$yam = sendToAmp($ip.':80/YamahaRemoteControl/ctrl','POST','/YamahaRemoteControl/ctrl',$commands["Z2_VOL_45"]);
	}	

?>
