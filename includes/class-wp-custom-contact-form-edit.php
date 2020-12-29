<?php
	$post_id = $_GET['page_id'];
	$post_detail = get_post($post_id);
	/*echo "<pre>";
		print_r($post_detail);
	echo "</pre>";*/
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];
	$contact_no = $_POST['contact_no'];
	$message = $_POST['message'];
	global $wpdb;
	$wpdb->update($wpdb->posts,array(
		'firstname' =>$first_name,
		'lastname' =>$last_name,
		'email'=>$email,
		'contact_no'=>$contact_no,
		'message'=>$message
	),array('id'=>$post_id));
	
	$wpdb->delete($wpdb->posts,array(
		'id'=>post_id
	));
?>
<h3>Post Data</h3>
<form method="post" name="frm_post_data">
	<p>
		<label>First Name</label>
		<input type="text" name="first_name" value=""/>
	</p>
	<p>
		<label>Last Name</label>
		<input type="text" name="last_name" value=""/>
	</p>
	<p>
		<label>Email</label>
		<input type="text" name="email" value=""/>
	</p>
	<p>
		<label>Contact Numer</label>
		<input type="text" name="contact_no" value=""/>
	</p>
	<p>
		<label>Message</label>
		<input type="text" name="message" value=""/>
	</p>
	<p>
		<input type="submit" name="txt_submit" value="Submit"/>
		<input type="reset" name="txt_reset" value="Reset"/>
	</p>
</form>