<?php // -*- xml -*-
$recent = isset($_COOKIE['recent']);
if (!$recent) setcookie('recent', 'yes', time() + 4320000, '/', '.pickuplist.com', false, true);
print('<'.'?xml version="1.0" encoding="utf-8" ?'.'>');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PickupList - find local pick-up sports games</title>
    <link href="reset.css" rel="stylesheet" type="text/css" />
    <link href="screen.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/script.js/1.3/script.min.js"></script>
    <script type="text/javascript" src="pickuplist.min.js"></script>
  </head>
  
  <body>
    <div id="top-bar"></div>
    <div id="page">
      <div id="header">
	<div id="title">PickupList</div>
	<div id="nav">
	  search for:
	  <input type="text" id="keywords" title="Enter keywords and press [Enter] to search the map." />
	  near:
	  <input type="text" id="location" title="Enter a location and press [Enter] to recenter the map." />
	</div>
      </div>
      
      <div id="map">
	<div class="top">
	  <div class="left"></div>
	  <div class="right"></div>
	</div>
	<div class="middle">
	  <div class="left"></div>
	  <div id="map_canvas"></div>
<?php if (!$recent) { // --------------------------------- ?>
	  <div id="content_overlay">
	    <div class="blanket"></div>
	    <div class="message">
	      <h1>Welcome!</h1>
	      <p>
		Here are some quick instructions:
	      </p>
	      <p>
		<strong>To search for a game,</strong> use the <em>search for</em> and <em>near</em> fields near the top of the page to search for a pick-up sports game in a specific location.
	      </p>
	      <p>
		<strong>To add a game,</strong> right-click on the map&mdash;anywhere&mdash;to post a new game at that location.  No account or sign-up is required!
	      </p>
	      <p>
		<a href="#closeoverlay">Take me to the map &raquo;</a>
	      </p>
	    </div>
	  </div>
<?php } // -----------------------------------------------?>
	  <div class="right"></div>
	</div>
	<div class="bottom">
	  <div class="left"></div>
	  <div class="right"></div>
	</div>
      </div>
    </div>
    
    <div id="footer">
      <div id="footer-inner">
	<div class="column">
	  <h3>What is PickupList?</h3>
	  <p>
	    Steve came up with the idea of PickupList when he was
	    traveling in an unfamiliar location and wanted to find a
	    game of pick-up ultimate frisbee.  So Steve and Ian put
	    their heads together to find a solution, and PickupList was
	    the result.
	  </p>
	  <p>
	    With PickupList, it's now easy to find local pick-up sports
	    games when you're in an unfamiliar area. <a href="@PickupList">Follow us on Twitter</a> to stay up-to-date with our newest development.
	  </p>
	</div>
	
	<div class="column">
	  <h3>How do I use PickupList?</h3>
	  <p>
	    It's simple!  Use the search and location boxes
	    above to locate a pick-up sports game near you.  You can
	    easily add a game by right-clicking on the map wherever
	    your game takes place&mdash;no account or sign-up is required!
	    Once you post a new game, you'll receive an email with
	    information on how to update or remove your post.
	  </p>
	  <p>
	    We're always adding new functionality&mdash;soon you'll be
	    able to search for games via <a href="search@">email</a> and
	    <a href="sms:+15085915478" title="508-591-LIST (5478)">SMS</a>.
	  </p>
	</div>
	
	<div class="column hidden">
	  <h3>Latest Tweets</h3>
	  <div id="twitter">
	    <!-- nothing here yet -->
	  </div>
	</div>

	<p id="copyright">
	  Copyright &copy; 2011 <a href="http://www.stevebroskey.com">Steve Broskey</a> and <a href="http://iancooper.name/">Ian Cooper</a>.
	  <a href="fineprint.html#privacy">Privacy Policy</a>.
	  <a href="fineprint.html#tos">Terms of Service</a>.
	</p>
      </div>
    </div>

    <div class="defaults hidden">
      <div id="display-game">
	<div class="infowindow">
	  {$2}
	  <div class="added-on">Added on {$3}</div>
	  <div class="actions">
	    <a href="/{$1}/directions">directions to here</a> -
	    <a href="/{$1}/contact">contact organizer</a> -
	    <a href="/{$1}/flag">flag</a>
	  </div>
	</div>
      </div>

      <div id="add-game">
	<div class="infowindow">
	  <div class="page1">
	    <strong>{$1}</strong>
	    <button class="save">Save</button><br />
	    <textarea class="default description" rows="5" cols="15" title="make sure to include things like schedule and equipment necessary">{$2}</textarea><br />
	    <input type="text" class="default email" title="you'll receive an email confirmation of your posting" value="{$3}" /><br />
	    <button class="save">Save</button>
	    <input type="hidden" class="latitude" value="{$4}" />
	    <input type="hidden" class="longitude" value="{$5}" />
	  </div>
	  <div class="page2">
	    <strong>New Activity</strong><br />
	    <p>
	      Please check your inbox for a confirmation email from
	      no-reply@pickuplist.com.  If you do not see a confirmation
	      email within the next few minutes, please check your spam
	      or junk folder.
	    </p>
	  </div>
	</div>
      </div>
    </div>
  </body>
</html>
