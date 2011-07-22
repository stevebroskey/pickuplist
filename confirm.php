<?php

require_once('google.php');

$id = $_GET['id'];

if (!isset($id)) {
  header("Location: http://pickuplist.com/");
  exit;
}

$tokens = ClientLogin('**************', '*********', 'fusiontables');
$fusion = new FusionTable($tokens['Auth']);
$success = false;

$continue = "http://pickuplist.com/$id";

$thankyou = '<h1>Thanks for confirming your post.</h1>
<p>
  Your new post will be live at
  <a href="'.$continue.'">'.$continue.'</a>
  in just a minute.  Share the link to spread the word about your game!
</p>
<p class="share">
  <a href="#facebook"><img src="facebook.png" border="0" alt="Post this to Facebook" title="Post this to Facebook" /></a>
  <a href="#tweet"><img src="twitter.png" border="0" alt="Post this to Twitter" title="Post this to Twitter" /></a>
  <a href="#email"><img src="email.png" border="0" alt="Email this to a friend" title="Email this to a friend" /></a>
</p>
<a class="continue" href="'.$continue.'">Continue &raquo;</a>';

$message = '<h1>Oops.</h1>
<p>
  We\'ve encountered an error.  Please try again later.
</p>
<a class="continue" href="'.$continue.'">Continue &raquo;</a>';


$db = new mysqli('**********', '*********', '***********', '***********');
if ($stmt = $db->prepare('select data, latitude, longitude from pickuplist where id = ? and posted = 0 and confirmed = 0;')) {
  $stmt->bind_param("s", $id);
  $stmt->execute();
  $stmt->bind_result($data, $latitude, $longitude);
  
  if ($stmt->fetch()) {
    if ($res = $fusion->query("insert into 1077209 (id, data, latitude, longitude) values ('"
				.$db->escape_string($id)       ."', '"
				.$db->escape_string($data)     ."', '"
				.$db->escape_string($latitude) ."', '"
				.$db->escape_string($longitude)."')")) {
      $stmt->close();
      if ($stmt2 = $db->prepare('update pickuplist set posted = 1, confirmed = 1 where id = ?;')) {
	$stmt2->bind_param("s", $id);
	if ($stmt2->execute()) {
	  $message = $thankyou;
	}
      }
    }
  } else {
    header("Location: /$id");
  }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <base href="/" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>PickupList - find local pick-up sports games</title>
    <link href="reset.css" rel="stylesheet" type="text/css" />
    <link href="screen.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/script.js/1.3/script.min.js"></script>
    <script type="text/javascript" src="pickuplist-aux.min.js"></script>
  </head>
  
  <body>
    <div id="top-bar"></div>
    <div id="page">
      <div id="header">
	<div id="title">PickupList</div>
      </div>
      
      <div id="map">
	<div class="top">
	  <div class="left"></div>
	  <div class="right"></div>
	</div>
	<div class="middle">
	  <div class="left"></div>
	  <div id="map_canvas"></div>
	  <div id="content_overlay">
	    <div class="blanket"></div>
	    <div class="message">
              <?php print($message); ?>
	    </div>
	  </div>
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
	    It's simple!  Use the <a href="#search">search tool</a>
	    above to locate a pick-up sports game near you.  You can
	    easily add a game by right-clicking on the map wherever
	    your game takes place&mdash;no registration is required!
	    Once you post a new game, you'll receive an email with
	    information on how to update or remove your post.
	  </p>
	  <p>
	    We're always adding new functionality&mdash;soon you'll be
	    able to search for games via
	    <a href="@PickupList">Twitter</a>,
	    <a href="search@">email</a>, and
	    <a href="sms:+15085915478" title="508-591-LIST (5478)">SMS</a>.
	  </p>
	</div>
	
	<div class="column hidden">
	  <h3>Latest Tweets</h3>
	  <div id="twitter">
	    <div id="myTweets" class="twitters"></div>
	  </div>
	</div>

	<p id="copyright">
          Copyright &copy; 2011 <a href="http://www.stevebroskey.com">Steve Broskey</a> and <a href="http://iancooper.name/">Ian Cooper</a>.
	  <a href="fineprint.html#privacy">Privacy Policy</a>.
	  <a href="fineprint.html#tos">Terms of Service</a>.
        </p>
      </div>
    </div>

    <div class="hidden">
      <div id="fb-root"></div>
    </div>
  </body>
</html>
