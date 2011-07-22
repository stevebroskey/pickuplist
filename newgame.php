<?php

// check inputs
$latitude = $_GET['latitude'];
$longitude = $_GET['longitude'];
$email = $_GET['email'];
$description = $_GET['description'];

if (!(isset($latitude, $longitude, $email, $description)) or
    ($latitude == "" or $longitude == "" or $email == "" or $description == "")) {
  print('{ "message": "failure at 0" }');
  exit;
}

$code = md5(uniqid($email));
$description = str_replace("\"", "\\\"", $description);
$email = str_replace("\"", "\\\"", $email);
$date = date('d-M-Y');

$data = "{ \"description\": \"$description\", \"added\": \"$date\", \"contact\": \"$email\" }";

// add the input to the queue
$db = new mysqli('**********', '**********', '*********', '***********');
if ($stmt = $db->prepare('insert into pickuplist (code, data, latitude, longitude, confirmed, posted, deleted, expires) values (?, ?, ?, ?, 0, 0, 0, date_add(now(), interval \'45 2\' day_hour));')) {
  $stmt->bind_param("ssss", $code, $data, $latitude, $longitude);
  $stmt->execute();
  $stmt->close();
  if ($stmt2 = $db->prepare('select counter from pickuplist where code = ?;')) {
    $stmt2->bind_param("s", $code);
    $stmt2->execute();
    $stmt2->bind_result($counter);
    if ($stmt2->fetch()) {
      $id = base_convert($counter, 10, 36);
      $stmt2->close();
      if ($stmt3 = $db->prepare('update pickuplist set id = ? where counter = ?')) {
	$stmt3->bind_param("ss", $id, $counter);
	$stmt3->execute();
	$stmt3->close();
	// set a confirmation email
	$message = "Thanks for adding a new post to PickupList.  Please save this email for future reference as it contains important links regarding your post.

Before your post can be displayed to everyone, it needs to be confirmed.  Posts are normally live within 20 minutes of confirmation.  Please click on the following link to confirm your post now.

http://pickuplist.com/confirm/$id

When your post is live it will be accessible at http://pickuplist.com/$id -- you can email or tweet this link to share it with your friends.

Your post will expire 45 days after it was created.  You can renew your post by clicking on the following link.  (You will also receive a reminder email 1 week before the post is permanently removed.)

http://pickuplist.com/renew/$id/$code

If you would like to permanently remove your post before the 45-day expiration, please click the following link.  Please note that you cannot undo this action.

http://pickuplist.com/delete/$id/$code

Please keep this email in a safe place.  If you forward this email, the recipient will have the ability to delete or renew your post.

Your post will be live on PickupList.com approximately 15 minutes following confirmation.

Thanks,
Steve <steve@pickuplist.com>
Ian <ian@pickuplist.com>
http://pickuplist.com/

Follow us on Twitter at @PickupList <http://twitter.com/pickuplist>!

--
This is a one-time email that was automatically generated when someone submitted a new post to PickupList with your email listed as the contact.  If you've received this email in error, please delete it--no further action is necessary.
";

	mail("$email", "PickupList post information (save this email)", $message, "From: PickupList <no-reply@pickuplist.com>\r\nCc: Ian Cooper <ian@pickuplist.com>");
	mail("ian@pickuplist.com", "PickupList post information (save this email)", $message, "From: PickupList <no-reply@pickuplist.com>\r\nCc: Ian Cooper <ian@pickuplist.com>");
	print('{ "message": "success" }');
      } else {
	print('{ "message": "failure at 4" }');
      }
    } else {
      print('{ "message": "failure at 3" }');
    }
  } else {
    print('{ "message": "failure at 2" }');
  }
} else {
  print('{ "message": "failure at 1" }');
}

?>