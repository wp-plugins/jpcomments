<?php
/*

Plugin Name: jpComments
Plugin URI: N/A
Description: Override the WordPress comment system to use only a users Twitter information
Version: 1.0
Author: Matt Vickers & Joey Primiani
Author URI: http://jprim.com
License: GPL2

	Copyright 2010  Matt Vickers & Joey Primiani  (email : matt@envexlabs.com | joeyprimiani@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/*

	 ######  ######## ######## ##     ## ########  
	##    ## ##          ##    ##     ## ##     ## 
	##       ##          ##    ##     ## ##     ## 
	 ######  ######      ##    ##     ## ########  
	      ## ##          ##    ##     ## ##        
	##    ## ##          ##    ##     ## ##        
	 ######  ########    ##     #######  ##  

*/

global $wpdb;
$jpc_tablename = $wpdb->prefix . 'jpcomments_twitter_info';

define('PLUGIN_PATH', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)));

/*

	We need to create the twitter info table when a user activates the plugin
	for the first time

*/
	
function jpc_init(){

	//Load up our javacript file
	wp_register_script( 'jpc_main', PLUGIN_PATH . 'jpc_main.js');

	//Check to see if we need to create the twitter info table
	global $wpdb, $jpc_tablename;
	
	$jpc_get_table = $wpdb->get_results("SHOW TABLES LIKE '{$jpc_tablename}'");
		
	if(!$jpc_get_table){
		
		//Create the table
		$create = $wpdb->query("CREATE TABLE IF NOT EXISTS {$jpc_tablename} (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `username` varchar(250) NOT NULL,
			  `info` text NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `id` (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20;");
	
	}
		
}

//Run the function on activation
add_action('init', 'jpc_init');

/*

	Load our custom javascript files

*/

function jpc_add_scripts(){

	if (!is_admin()){
		wp_enqueue_script('jquery');
		wp_enqueue_script('jpc_main', PLUGIN_PATH . '/jpc_main.js', array('jquery'),'1.0.0',1);
	}

}

add_action('wp_print_scripts', 'jpc_add_scripts');


/*

	   ###    ########  ##     ## #### ##    ## 
	  ## ##   ##     ## ###   ###  ##  ###   ## 
	 ##   ##  ##     ## #### ####  ##  ####  ## 
	##     ## ##     ## ## ### ##  ##  ## ## ## 
	######### ##     ## ##     ##  ##  ##  #### 
	##     ## ##     ## ##     ##  ##  ##   ### 
	##     ## ########  ##     ## #### ##    ## 

*/

// create custom plugin settings menu
add_action('admin_menu', 'jpc_create_menu');

function jpc_create_menu() {

	//create new sub menu
	add_submenu_page( 'options-general.php', 'jpComments', 'jpComments Settings', 'administrator', __FILE__, 'jpc_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'jpc_settings_groups', 'jpc_twitter_thumbnail_size' );
}

function jpc_settings_page() { ?>

	<div class="wrap">
	
		<h2>jpComments</h2>
		
		<p>Welcome to the jpComments settings page. Please take a moment to collect yourself and we can continue.</p>
		
		<form method="post" action="options.php">
		
		    <?php settings_fields( 'jpc_settings_groups' ); ?>
		    
		    <table class="form-table">
		        <tr valign="top">
			        <th scope="row">Twitter Thumbnail Size</th>
			        <td>
			        	<select name="jpc_twitter_thumbnail_size">
			        		<option value="m">24 x 24</option>
			        		<option value="n">48 x 48</option>
			        		<option value="b">73 x 74</option>
			        	</select>
			        	<p><em>Based on the service by: <a href="http://twitter.com/joestump" target="_blank">Joestump</a>'s <a href="http://tweetimag.es/" target="_blank">tweetimag.es</a></em></p>
			        </td>
		        </tr>
		
		    </table>
		    
		    <p class="submit">
		    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		    </p>
		
		</form>
	
	</div> <!-- wrap -->

<?php 
}

/*

	 ######   #######  ##     ## ##     ## ######## ##    ## ########  ######  
	##    ## ##     ## ###   ### ###   ### ##       ###   ##    ##    ##    ## 
	##       ##     ## #### #### #### #### ##       ####  ##    ##    ##       
	##       ##     ## ## ### ## ## ### ## ######   ## ## ##    ##     ######  
	##       ##     ## ##     ## ##     ## ##       ##  ####    ##          ## 
	##    ## ##     ## ##     ## ##     ## ##       ##   ###    ##    ##    ## 
	 ######   #######  ##     ## ##     ## ######## ##    ##    ##     ######  


*/


/*

	Time since function
	To display a nice timestamp on the twitter comments

*/

function time_since($original) {
    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
    );
    
    $today = time(); /* Current unix time  */
    $since = $today - $original;
	
	if($since > 604800) {
		$print = date("M jS", $original);
	
		if($since > 31536000) {
				$print .= ", " . date("Y", $original);
			}

		return $print;

	}
    
    // $j saves performing the count function each time around the loop
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        
        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

    return $print . " ago";

}


/*

	Get a users twitter information
	If the user isn't in the DB add them, otherwise pull their data
	
*/

function get_twitter_info($username){

	global $wpdb, $jpc_tablename;

	/*
	
	Clean this up
	
	*/
	
	$user_info = array();
	
	//Check if the user is already in the DB
	$is_user = $wpdb->get_row("
		SELECT `username` FROM {$jpc_tablename}
		WHERE `username` = '$username'
	");
		
	if($is_user){
	
		//In the db, return info on file
		$get_user = $wpdb->get_row("
			SELECT * FROM {$jpc_tablename}
			WHERE `username` = '$username'
		");
		
		$twitter_info = unserialize($get_user->info);
				
	}else{

		$rate_limit = json_decode(@file_get_contents('http://api.twitter.com/account/rate_limit_status.json'));
								
		if($rate_limit->remaining_hits <= 0){
				
			return false;
		
		}
					
		//Not in the db, input into the db and return info
		$twitter_info = json_decode(@file_get_contents('http://api.twitter.com/1/users/show.json?id=' . $username));

		$serialized = serialize($twitter_info);
		
		$wpdb->query( $wpdb->prepare( "
			INSERT INTO {$jpc_tablename}
			( id, username, info )
			VALUES ( %d, %s, %s )", 
		        null, $username, $serialized ) );
	
	}

	$user_info['name'] = !empty($twitter_info->name) ? $twitter_info->name : $username;
	$user_info['location'] = $twitter_info->location;
	$user_info['url'] = $twitter_info->url;

	return $user_info;
			
}

/*

	New Comment Template
	We want to over-ride the default WordPress comment template with out newer, sexier version

*/

function jpc_comment_template(){

	//I think we can just link to a file
	return dirname( __FILE__ ) . '/jpc_comment_template.php';
}

add_filter('comments_template', 'jpc_comment_template');

function jpc_comments($comment, $args, $depth){

	$GLOBALS['comment'] = $comment;
	
	$color_author = array();

	$author = get_comment_author();
	
	$user_info = get_twitter_info($author);
	
	//Randomize colors
	$colors = array('blue','green','yellow','orange');
	//$color = $colors[rand(0,2)];
		
	//Check if the user is already in the array		
	if(!array_key_exists($author, $color_author)){
	
		//Check if the post is by joey
		$color = $author == 'jp' ? 'red' : $colors[rand(0,3)];

		$color_author[$author] = $color;
	}
	
	$user_link = !empty($user_info['url']) ? $user_info['url'] : 'http://twitter.com/'.$author;
		
	?>
	
	<li class="<?php echo $color_author[$author]; ?>" id="comment-<?php comment_ID() ?>">
	
		<div class="twitter_av">
			
			<img src="http://img.tweetimag.es/i/<?php echo $author; ?>_<?php echo get_option('jpc_twitter_thumbnail_size'); ?>" alt="<?php echo $author; ?>" />
			
		</div> <!-- twitter_av -->
		
		<div class="comment_text">
		
			<h3><a href="<?php echo $user_link; ?>" target="_blank"><?php echo $user_info['name']; ?></a></h3>
		
			<?php comment_text() ?>

			<?php
			$seconds = get_comment_date('U'); ?>
			
			<p class="since">
				<em>
					<?php echo time_since($seconds); ?>
					<?php
					if(!empty($user_info['location'])): ?>
						from <?php echo $user_info['location']; ?>
					<?php
					endif; ?>
				</em>
			</p>

		</div> <!-- comment_text -->
	
	</li>

<?php

}