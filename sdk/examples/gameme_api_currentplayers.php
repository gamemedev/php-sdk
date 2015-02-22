<?php
	
	/**
	 * gameME SDK
	 * Webpage: http://www.gameme.com
	 * Docs: http://www.gameme.com/docs/api/sdk
	 * Copyright (C) 2011-2013 TTS Oetzel & Goerz GmbH
	 * 
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation; either version 2
	 * of the License, or (at your option) any later version.
	 * 
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 * 
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
	 * 
	 * 
	 * All API results are limited to personal, non-commercial use only 
	 * Copyright(C) gameME 2011-2013 TTS Oetzel & Goerz GmbH. All rights reserved.
	 */

	require("gameme_api_sdk.php");
	$gameME_sdk_object = new gameMEAPI(YOUR_URL_HERE);  

	$current_slots  = 0;
	$maximum_slots  = 0;
	$current_bots   = 0;
	$total_servers  = 0;
	try {

		$server_list = $gameME_sdk_object->client_api_serverlist(GAMEME_FILTER_NONE);
		foreach($server_list['serverlist'] as $server) {
			$current_slots += $server['act'];
			$current_bots  += $server['bots'];
			$maximum_slots += $server['max'];
			$total_servers++;
		}

	} catch (Exception $e) {
		die ("Client API Serverlist Error: ".$e->getMessage()."\n");
	}
	
	print "Currently ".$current_slots."/".$maximum_slots." players (".$current_bots." bots) on ".$total_servers." gameservers\n";


?>