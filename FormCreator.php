<?php
/*
 Plugin Name: Gameplayform
 Plugin URI: http://darcade.de/
 Description: Plugin zum bereitstellen einer Form, um Gameplays zu versenden.
 Version: 0.0.1
 Author: Darcade
 Author URI: http://darcade.de/
 Update Server: http://gameplayform.darcade.de/
 Min WP Version: 1.5
 Max WP Version: 2.0.4
 */

$plugintablename = "darcadetable";
$gameplayformdbversion = "1.0";

if (!class_exists("GameplayFormClass")) {

	class GameplayFormClass {

		public static function doFrontendController() {
			static $form;
			if (!isset($form)) {
				$form = new GameplayFormClass();
				$form -> frontendController();
			}
			return $form;
		}

		public function frontendController() {

			switch ($_SERVER['REQUEST_METHOD']) {
				case 'POST' :
					$this -> datenAbspeichern();
					break;
				case 'GET' :
					ob_start();
					$datei = __DIR__ . DIRECTORY_SEPARATOR . 'formular.php';
					ob_end_clean();
					include $datei;
					break;
			}
		}

		//FIXME: add message
		public function datenAbspeichern() {
			//var_dump($_POST);
			global $wpdb, $plugintablename;
			$dbname = $wpdb -> prefix . $plugintablename;
			$wpdb -> insert($dbname, $_POST);
			//$wpdb->insert($dbname, array( 'game'=>'test1'));
			echo '<br>speichere';
			echo $dbname;

		}

		private function showtable($dboutarray) {
			$firstrun = TRUE;

			echo '<table class="wp-list-table">';

			foreach ($dboutarray as $key => $value) {
				if ($firstrun) {
					echo "<thead>";
					foreach ($value as $key2 => $value2) {
						echo "<th>$key2</th>";
					}
					echo "</thead> <tr>";
				}
				echo "<tr>";

				foreach ($value as $key2 => $value2) {
					echo "<th>$value2</th>";
				}
				echo "</tr>";

				$firstrun = FALSE;
			}

		}

		public function admininterface() {
			global $wpdb, $plugintablename;
			//$wptablevar = new GameplayFormClassTable;

			//set tablename
			$dbname = $wpdb -> prefix . $plugintablename;

			//output database
			$query = 'SELECT * FROM ' . $dbname;
			$dboutput = $wpdb -> get_results($query, ARRAY_A);

			echo '<div class="wrap">
				<div>
					<h1>Gameplayform</h1>
					<p>Table:</p>';

			//$wptablevar->showtable($dboutput);

			$this -> showtable($dboutput);

			echo "<br></div>
			</div><br>";
		}

		private function createtable() {
			global $wpdb, $plugintablename, $gameplayformdbversion;

			$dbname = $wpdb -> prefix . $plugintablename;
			$sql = "CREATE TABLE `" . $dbname . "` (
				`game` text NOT NULL,
				`isithd` text NOT NULL,
				`laenge` text NOT NULL,
				`spieleablauf` longtext NOT NULL,
				`endscore` text NOT NULL,
				`klasse` text NOT NULL,
				`dllink` text NOT NULL,
				`audiovorkommen` text NOT NULL,
				`dllinkaudio` text NOT NULL,
				`sonstiges` longtext NOT NULL,
				`kanal` text NOT NULL
				);";

			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			add_option("gameplayformdb_version", $gameplayformdbversion);
		}

		public function installgpplugin() {
			$this -> createtable();
		}

	}

}
/*
 if (!class_exists("GameplayFormClassTable")) {
 class GameplayFormClassTable extends WP_List_Table {
 public function showtable($dboutarray) {
 $this -> _column_headers = array($dboutarray, // columns
 array(), // hidden
 array(), // sortable
 );
 }

 }

 }
 */
$runvariable = new GameplayFormClass;

//*********************************************************************
//**********************FIXME******************************************
//*********************************************************************

function addadmininterface() {
	global $runvariable;
	add_menu_page('Gameplayform', 'Gameplayform', manage_options, __FILE__, array(&$runvariable, 'admininterface'));
}

//$runvariable->installgpplugin();
add_shortcode('gameplayform', array('GameplayFormClass', 'doFrontendController'));
add_action('admin_menu', 'addadmininterface');
?>

