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

	/**
	 * Timing Functions
	 */
    $start_time_array = array();
    function start_timing() {
      global $start_time_array;
      list($low, $high)   = explode(" ", microtime());
      $start_time_array[] = $high + $low;
    }
    start_timing();
  
    function stop_timing($digits = 6)  {
      global $start_time_array;
      $start_time = array_pop($start_time_array);
      list($low, $high) = explode(" ", microtime());
      $diff_time = ($high + $low) - $start_time;
      return sprintf("%0.".$digits."f", $diff_time);
    }


	/**
	 * Class to test all game global API calls
	 */

	class TestgameMEAPI {

		/**
		 * Global API object 
		 */		
		private $api_object;


		/**
		 * Testing variables
		 */		
		
		private $test_client_game;
		private $test_client_servers;
		private $test_client_players;
		private $test_global_servers;
		private $test_global_players;


		function TestgameMEAPI() {

			start_timing();
			
			$this->api_object = new gameMEAPI("");
			try {
				$test_api = $this->api_object->global_api_serverlist(GAMEME_FILTER_NONE, "", 1);
				$this->api_object->client_api_url = $test_api['serverlist'][0]['url']."/api";
				echo "Test-API: ".$this->api_object->client_api_url."\n";
			} catch (Exception $e) {
				die ("Client API Serverlist Error: ".$e->getMessage()."\n");
			}


			$this->check_client_serverlist();
			$this->check_client_serverinfo();
			$this->check_client_playerlist();
			// $this->check_client_full_playerlist();
			$this->check_client_playerinfo();
			
			/*
			 * Disable advanced checks by default
			 * 
			$this->check_client_voiceserver_status();
			$this->check_client_awards();
			$this->check_client_ribbons();
			$this->check_client_ribboninfo();
			$this->check_client_ranks();
			$this->check_client_rankinfo();
			*/

			$this->check_global_serverlist();
			$this->check_global_serverinfo();
			$this->check_global_playerlist();
			$this->check_global_playerinfo();

			print "[#TOT: ".stop_timing(3)."s] Tests completed\n";

		}

		
		/**
		 * Client API testing functions
		 * 
		 */

		function check_client_serverlist() {
			try {
				start_timing();
				$serverlist = $this->api_object->client_api_serverlist(GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_MULTIPLE_SERVERINFO_LIMIT, GAMEME_SORT_NAME_ASC, GAMEME_HASH_ADDRESS);
				foreach ($serverlist['serverlist'] as $server_address => $server_data) {
					$this->test_client_servers[] = $server_address;
					if ($this->test_client_game == "") {
						$this->test_client_game = $server_data['game'];
					}
				}
				print "[#C1: ".stop_timing(3)."s] Test Client Serverlist: Found ".count($this->test_client_servers)." servers [".join(",", $this->test_client_servers)."]\n";
			} catch (Exception $e) {
				die ("Client API Serverlist Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_serverinfo() {
			try {
				start_timing();
				$serverinfo = $this->api_object->client_api_serverinfo($this->test_client_servers[0]);
				$serverinfo = $this->api_object->client_api_serverinfo($this->test_client_servers);
				print "[#C2: ".stop_timing(3)."s] Test Client Serverinfo: Found ".count($serverinfo['serverinfo'])." servers\n";
			} catch (Exception $e) {
				die ("Client API Serverinfo Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_playerlist() {
			try {
				start_timing();
				$playerlist = $this->api_object->client_api_playerlist($this->test_client_game, GAMEME_FILTER_NONE, "", 5, GAMEME_SORT_DEFAULT, GAMEME_HASH_UNIQUEID);
				foreach ($playerlist['playerlist'] as $player_uniqueid => $player_data) {
					$this->test_client_players[] = $player_uniqueid;
				}
				print "[#C3: ".stop_timing(3)."s] Test Client Playerlist: Found ".count($this->test_client_players)." players [".join(",", $this->test_client_players)."]\n";
			} catch (Exception $e) {
				die ("Client API Playerlist Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_full_playerlist() {
			try {
				start_timing();
				$full_playerlist = $this->api_object->client_api_full_playerlist($this->test_client_game, GAMEME_FILTER_NONE, "", GAMEME_SORT_DEFAULT, GAMEME_HASH_UNIQUEID);
				print "[#C4: ".stop_timing(3)."s] Test Client Full Playerlist: Found ".count($full_playerlist['playerlist'])." players\n";
			} catch (Exception $e) {
				die ("Client API Playerlist Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_playerinfo() {
			try {
				start_timing();
				$playerinfo = $this->api_object->client_api_playerinfo($this->test_client_game, $this->test_client_players[0], GAMEME_DATA_DEFAULT, GAMEME_HASH_UNIQUEID);
				$playerinfo = $this->api_object->client_api_playerinfo($this->test_client_game, $this->test_client_players, GAMEME_DATA_DEFAULT, GAMEME_HASH_UNIQUEID);
				print "[#C5: ".stop_timing(3)."s] Test Client Serverinfo: Found ".count($playerinfo['playerinfo'])."/".count($this->test_client_players)." players\n";
			} catch (Exception $e) {
				die ("Client API Playerinfo Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_voiceserver_status() {
			try {
				start_timing();
				$voiceserver_status = $this->api_object->client_api_voiceserver_status();
				if ((isset($voiceserver_status['voiceserver'])) && (is_array($voiceserver_status['voiceserver']))) {
					print "[#C6: ".stop_timing(3)."s] Test Client Voiceserver Status: Found ".$voiceserver_status['voiceserver'][0]['clientscount']."/".$voiceserver_status['voiceserver'][0]['maxclients']." clients\n";
				} else {
					print "[#C6: ".stop_timing(3)."s] Test Client Voiceserver Status: No voiceserver available\n";
				}
			} catch (Exception $e) {
				die ("Client API Vocieserver Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_awards() {
			try {
				start_timing();
				$awards = $this->api_object->client_api_awards($this->test_client_game);
				$live_awards = $this->api_object->client_api_awards($this->test_client_game, GAMEME_FILTER_DATE, "live");
				print "[#C7: ".stop_timing(3)."s] Test Client Awards: Found ".count($awards['awards'])."x awards, ".count($live_awards['awards'])."x live-awards\n";
			} catch (Exception $e) {
				die ("Client API Awards Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_ribbons() {
			try {
				start_timing();
				$ribbons = $this->api_object->client_api_ribbons($this->test_client_game);
				print "[#C8: ".stop_timing(3)."s] Test Client Ribbons: Found ".count($ribbons['ribbons'])."x ribbons\n";
			} catch (Exception $e) {
				die ("Client API Ribbons Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_ribboninfo() {
			try {
				start_timing();
				$ribboninfo = $this->api_object->client_api_ribboninfo($this->test_client_game, GAMEME_FILTER_RIBBONCODE, "longest_playtime");
				print "[#C9: ".stop_timing(3)."s] Test Client Ribboninfo: Found ".count($ribboninfo['ribboninfo'])."x players\n";
			} catch (Exception $e) {
				die ("Client API Rankinfo Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_ranks() {
			try {
				start_timing();
				$ranks = $this->api_object->client_api_ranks($this->test_client_game);
				print "[#C10: ".stop_timing(3)."s] Test Client Ranks: Found ".count($ranks['ranks'])."x ranks\n";
			} catch (Exception $e) {
				die ("Client API Ranks Error: ".$e->getMessage()."\n");
			}
		}


		function check_client_rankinfo() {
			try {
				start_timing();
				$rankinfo = $this->api_object->client_api_rankinfo($this->test_client_game, GAMEME_FILTER_RANKID, 1);
				print "[#C11: ".stop_timing(3)."s] Test Client Rankinfo: Found ".count($rankinfo['rankinfo'])."x players\n";
			} catch (Exception $e) {
				die ("Client API Rankinfo Error: ".$e->getMessage()."\n");
			}
		}


		/**
		 * Global API testing functions
		 * 
		 */
		
		function check_global_serverlist() {
			try {
				start_timing();
				$serverlist = $this->api_object->global_api_serverlist(GAMEME_FILTER_NONE, "", GAMEME_GLOBALAPI_MULTIPLE_SERVERINFO_LIMIT, GAMEME_SORT_DEFAULT, GAMEME_HASH_ADDRESS);
				foreach ($serverlist['serverlist'] as $server_address => $server_data) {
					$this->test_global_servers[] = $server_address;
				}
				print "[#G1: ".stop_timing(3)."s] Test Global Serverlist: Found ".count($this->test_global_servers)." servers [".join(",", $this->test_global_servers)."]\n";
			} catch (Exception $e) {
				die ("Global API Serverlist Error: ".$e->getMessage()."\n");
			}
		}


		function check_global_serverinfo() {
			try {
				start_timing();
				$serverinfo = $this->api_object->global_api_serverinfo($this->test_global_servers[0]);
				$serverinfo = $this->api_object->global_api_serverinfo($this->test_global_servers);
				print "[#G2: ".stop_timing(3)."s] Test Global Serverinfo: Found ".count($serverinfo['serverinfo'])." servers\n";
			} catch (Exception $e) {
				die ("Global API Serverinfo Error: ".$e->getMessage()."\n");
			}
		}


		function check_global_playerlist() {
			try {
				start_timing();
				$playerlist = $this->api_object->global_api_playerlist(GAMEME_GAME_CSS, GAMEME_FILTER_NONE, "", 5, GAMEME_SORT_DEFAULT, GAMEME_HASH_UNIQUEID);
				foreach ($playerlist['playerlist'] as $player_uniqueid => $player_data) {
					$this->test_global_players[] = $player_uniqueid;
				}
				print "[#G3: ".stop_timing(3)."s] Test Global Playerlist: Found ".count($this->test_global_players)." players [".join(",", $this->test_global_players)."]\n";

			} catch (Exception $e) {
				die ("Global API Playerlist Error: ".$e->getMessage()."\n");
			}
		}


		function check_global_playerinfo() {
			try {
				start_timing();
				$playerinfo = $this->api_object->global_api_playerinfo(GAMEME_GAME_CSS, $this->test_global_players[0], GAMEME_DATA_DEFAULT, GAMEME_HASH_UNIQUEID);
				$playerinfo = $this->api_object->global_api_playerinfo(GAMEME_GAME_CSS, $this->test_global_players, GAMEME_DATA_DEFAULT, GAMEME_HASH_UNIQUEID);
				print "[#G4: ".stop_timing(3)."s] Test Global Serverinfo: Found ".count($playerinfo['playerinfo'])."/".count($this->test_global_players)." players\n";
			} catch (Exception $e) {
				die ("Global API Playerinfo Error: ".$e->getMessage()."\n");
			}
		}
	}

	/**
	 * Auto-Run test script (disabled by default)
	 */
	
	// new TestgameMEAPI();
?>