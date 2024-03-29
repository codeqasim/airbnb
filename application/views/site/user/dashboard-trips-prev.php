<?php 
$this->load->view('site/templates/header');
$today =  date('Y-m-d',strtotime(date('Y-m-d', strtotime("-2 days"))));
$today = $today.' 00:00:00';
?>
<style>
.btn-create.create-button{
	display:block;
}
.review_img{
	background: url(images/no-rating_star.png) repeat-x;
	float: left;
	height: 17px;
	width: 86px;
	position: absolute;
	background-size: 14.1px auto !important;
}
.review_st{
	background: url(images/rating_star.png) repeat-x;
	float: left;
	height: 17px;
	position: relative;
	padding: 10px 0;
}
.right-review{
	float: right;
	width: 82%;
	margin-top: 10px;
}
.right-review li {
	float: left;
	width: 48%;
	padding:0 0 0 30px;
	border-bottom:none;
}
.right-review span {
	color: #333;
	float: left;
	font-family: opensansregular;
	font-size: 13px;
	text-align: left;
	width: 50%;
	font-weight: bold;
}
</style>
<script src="js/site/<?php echo SITE_COMMON_DEFINE ?>jquery.colorbox.js"></script>
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.stars.min.js"></script>
<link type="text/css" rel="stylesheet" href="css/jquery.ui.stars.min.css" />
<script src="js/jRating.jquery.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/jRating.jquery.css" type="text/css" /> 
<link rel="stylesheet" href="css/site/my-account.css" type="text/css" />
<script src="js/site/jquery-ui-1.8.18.custom.min.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="css/site/jquery-ui-1.8.18.custom.css" />
<!---DASHBOARD-->
<div class="dashboard yourlisting trip bgcolor">
<div class="top-listing-head">
	<div class="main">   
		<ul id="nav">
			<li><a href="<?php echo base_url();?>dashboard"><?php if($this->lang->line('Dashboard') != '') { echo stripslashes($this->lang->line('Dashboard')); } else echo "Dashboard";?></a></li>
			<li><a href="<?php echo base_url();?>inbox"><?php if($this->lang->line('Inbox') != '') { echo stripslashes($this->lang->line('Inbox')); } else echo "Inbox";?></a></li>
			<li><a href="<?php echo base_url();?>listing/all"><?php if($this->lang->line('YourListing') != '') { echo stripslashes($this->lang->line('YourListing')); } else echo "Your Listing";?></a></li>
			<li class="active"><a href="<?php echo base_url();?>trips/upcoming"><?php if($this->lang->line('YourTrips') != '') { echo stripslashes($this->lang->line('YourTrips')); } else echo "Your Trips";?></a></li>
			<li><a href="<?php echo base_url();?>settings"><?php if($this->lang->line('Profile') != '') { echo stripslashes($this->lang->line('Profile')); } else echo "Profile";?></a></li>
			<li><a href="<?php echo base_url();?>account"><?php if($this->lang->line('Account') != '') { echo stripslashes($this->lang->line('Account')); } else echo "Account";?></a></li>
			<li><a href="<?php echo base_url();?>plan"><?php if($this->lang->line('Plan') != '') { echo stripslashes($this->lang->line('Plan')); } else echo "Plan";?></a></li>
		</ul>
	</div>
</div>
<div class="main">
	<div id="command_center">
		<ul class="subnav">
			<li><a href="<?php echo base_url();?>trips/upcoming"><?php if($this->lang->line('YourTrips') != '') { echo stripslashes($this->lang->line('YourTrips')); } else echo "Your Trips";?></a></li>
			<li class="active"><a href="<?php echo base_url();?>trips/previous"><?php if($this->lang->line('PreviousTrips') != '') { echo stripslashes($this->lang->line('PreviousTrips')); } else echo "Previous Trips";?></a></li>
		</ul>
		<div class="current_trips" id="trips">
			<div id="account">
				<div class="box">
					<div class="middle">
						<div style="margin:0;padding:0;display:inline"></div>
						<h2><?php if($this->lang->line('YourTrips') != '') { echo stripslashes($this->lang->line('YourTrips')); } else echo "Your Trips";?></h2>
						<div class="section notification_section">
							<div class="notification_area">
								<form method="post" action="">
									<input type="text" placeholder="Where are you going?" name="product_title" value="">
									<input class="sesarch-areas-btn" type="submit" value="Search">
								</form>
								<?php if($bookedRental->num_rows() >0){?>
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="member_ship" id="productListTable">
								<thead>
									<tr height="40px">
										<td style="width:100px"><strong><strong><?php if($this->lang->line('BookedOn') != '') { echo stripslashes($this->lang->line('BookedOn')); } else echo "Booked On";?></strong></td>
										<td style="width:100px"><strong><?php if($this->lang->line('PropertyName') != '') { echo stripslashes($this->lang->line('PropertyName')); } else echo "Property Name";?></strong></td>
										<td style="width:100px"><strong><?php if($this->lang->line('Host') != '') { echo stripslashes($this->lang->line('Host')); } else echo "Host";?></strong></td>
										<td style="width:140px"><strong><?php if($this->lang->line('DatesandLocation') != '') { echo stripslashes($this->lang->line('DatesandLocation')); } else echo "Dates and Location";?></strong></td>
										<td style="width:100px"><strong><?php if($this->lang->line('Amount') != '') { echo stripslashes($this->lang->line('Amount')); } else echo "Amount";?></strong></td>
										<td style="width:50px"><strong>Status</strong></td>
									</tr>
								</thead>
								<?php foreach($bookedRental->result() as $row){ 
								$paymentstatus = $this->cms_model->get_all_details(PAYMENT,array('Enquiryid'=>$row->cid));
								$chkval = $paymentstatus->num_rows();
								$status = $paymentstatus->row()->status;?>
								<tbody>
									<tr>
										<td><?php echo date('M d, Y',strtotime($row->dateAdded));?></td>
										<td><img src="<?php echo base_url(); ?>server/php/rental/<?php if($row->product_image == '') echo "dummyProductImage.jpg"; else echo $row->product_image;?>" width="100" height="100" alt="<?php echo $row->product_title; ?>"/> <a target="_blank" href="<?php echo base_url(); ?>rental/<?php echo $row->product_id; ?>" style="float:left;"></br><?php echo $row->product_title; ?></a></td>
										<td><a target="_blank" href="users/show/<?php echo $row->renter_id; ?>" style=""><?php echo $row->firstname." ".$row->lastname;?></a></td>
										<td class="area-tags"> <?php  if($row->checkin!='0000-00-00 00:00:00' && $row->checkout!='0000-00-00 00:00:00'){ echo "<br>".date('M d',strtotime($row->checkin))." - ".date('d, Y',strtotime($row->checkout))."<br>";if($row->product_title !=''){
										echo "<a href='".base_url()."rental/".$row->product_id."'>".$row->product_title."</a><br>";}
										echo "<label style='font-weight:bold;'>Booking No :</label>".$row->bookingno;}?>
										</td>
										<td><?php if($row->offer > 0){
										echo strtoupper($currencySymbol)." ".number_format($row->totalAmt*$this->session->userdata('currency_r'),2);
										echo '<li style="text-decoration: line-through;">'.strtoupper($currencySymbol)." ".number_format($row->offer*$this->session->userdata('currency_r'),2).'</li>'; }else{ echo strtoupper($currencySymbol)." ".number_format($row->totalAmt*$this->session->userdata('currency_r'),2);	} ?> 
										</td>
										<td><p style=" color: #000; font-weight: bold; "><?php if($row->dateAdded < $today) { echo ($status=="Paid")?"Booked":"Expired"; } else { echo ($status=="Paid")?"Booked":"Pending"; } ?> </p> <?php if($status=="Paid"){ ?> <a href="site/user/invoice/<?php echo  $row->bookingno; ?>" target="_blank"><?php if($this->lang->line('Confirmation') != '') { echo stripslashes($this->lang->line('Confirmation')); } else echo "Confirmation";?></a> <?php  $this->data['reviewData_all'] = $this->product_model->get_trip_review_all($userDetails->row()->id); $this->data['reviewData'] = $this->product_model->get_trip_review($row->bookingno,$userDetails->row()->id); if($this->data['reviewData']->num_rows==0) { ?> <a data-toggle="modal" href="#add_review" onclick="return add_data(this)" user_id="<?php echo $row->renter_id;?>" product_id="<?php echo $row->product_id;?>" reviewer_id="<?php if($userDetails!='no') { echo $userDetails->row()->id; }?>" booking_no="<?php if($userDetails!='no') { echo $row->bookingno; }?>" class="btn"><?php if($this->lang->line('Review') != '') { echo stripslashes($this->lang->line('Review')); } else echo "Review";?></a> <?php }else {?> <a data-toggle="modal" href="#display_review" onclick="return booking_review(this)"  user_id="<?php echo $row->renter_id;?>" product_id="<?php echo $row->product_id;?>" booking_no="<?php echo $row->bookingno;?>" reviewer_id="<?php if($userDetails!='no') { echo $userDetails->row()->id; }?>" class="btn"><?php if($this->lang->line('YourReview') != '') { echo stripslashes($this->lang->line('YourReview')); } else echo "Your Review";?></a>
										<?php }?>					
										<a data-toggle="modal" href="#inbox" onclick="return post_discussion(this)" renter_id="<?php echo $row->renter_id;?>" product_id="<?php echo $row->product_id;?>" booking_no="<?php echo $row->bookingno;?>" reviewer_id="<?php if($userDetails!='no') { echo $userDetails->row()->id; }?>"><?php if($this->lang->line('Message') != '') { echo stripslashes($this->lang->line('Message')); } else echo "Message";?></a>
										 <?php } ?>
										</td>
									</tr>
								</tbody>
								<?php   } ?>
								</table>
								<?php } else { ?> <p><?php if($this->lang->line('Youhaveno') != '') { echo stripslashes($this->lang->line('Youhaveno')); } else echo "You have no current trips.";?> <br><br></p><?php } ?>


								<div class="notification_action">
									<input id="id" type="hidden" name="id" value="ramasamy@teamtweaks.com">
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>       
	</div>
</div>

<div id="add_review" class="modal in" style="overflow: hidden; display: none; height: 280px !important;" aria-hidden="false">
<div class="modal-header" style="background: none repeat scroll 0% 0% rgb(239, 239, 239); padding: 18px 10px 18px 40px;"><div ><b>Add Review</b><a style="float:right; padding-right: 30px;cursor: pointer;"; data-dismiss="modal"><span class="">X</span></a></div></div>
<?php if($loginCheck !=''){ echo form_open('site/product/add_review',array('id'=>'reviewForm','style'=>'margin: 20px 20px 20px 40px'));?>
<input type="hidden" id="proid" name="proid" value="" />
<input type="hidden" id="user_id" name="user_id" value="" />
<input type="hidden" id="booking_no" name="bookingno" value="" />
<label>My review<span>*</span>
<span style="font-size:10px; color:#666;"> (Exclude personally identifiable information such as name, email address, etc)</span></label>
<textarea  name="review" id="review-text" class="scroll_newdes" maxlength="300" style="height:90px; width: 440px;"></textarea>
<div id="review_warn"  style="float:left; color:#FF0000;"></div>
<div class="clear"></div>
<input type="hidden" name="rating" id="r1"/>
<input type="hidden" name="total_review" id="r7" value=""/>
<div class="star_rating starares">
<li><label>Rating</label><div class="exemple5" data-id="r1" id="r11" data="10_5"  style="width:60%;"></div></li>
</div>
<div class="field_login" style=" margin-top:10px;">
  <input type="hidden" name="reviewer_id" value="<?php if($userDetails!='no') { echo $userDetails->row()->id; }?>" />
  <input type="hidden" name="reviewer_email" value="<?php if($userDetails!='no') { echo $userDetails->row()->email; }?>" />
  <input type="button" name="Review" id="Review"  onClick="add_review()"  style="float: right; background: none repeat scroll 0% 0% rgb(52, 129, 201); color: rgb(255, 255, 255); text-shadow: 0px 0px 0px rgb(255, 255, 255);" value="Submit my review">
</div>
<?php echo form_close();}?>
</div>



<div id="display_review" class="modal in" style="overflow: hidden; display: none; height: 260px !important;" aria-hidden="false">
<div class="modal-header" style="background: none repeat scroll 0% 0% rgb(239, 239, 239); padding: 18px 10px 18px 40px;"><div ><b>Your Review</b><a style="float:right; padding-right: 30px;cursor: pointer;"; data-dismiss="modal"><span class="">X</span></a></div></div>
<ul class="list-paging" style ="margin: 10px 10px 10px 10px">
<?php 
$reviewData = $this->data['reviewData_all'];
if($reviewData !=''){foreach($reviewData->result_array() as $review ):?>
<li class="<?php echo $review['bookingno'];?> mainli" style="display:none";>
<div class="peps">
<figure class="peps-area">
<img src="<?php if($review['image'] == '')echo base_url().'images/site/profile.png'; else echo 'images/users/'.$review['image']; ?>">
</figure>
<span class="johns" style="width:58%;"><b><?php echo $review['firstname']?><br/>(Your Review)</b></span>
</div>

<div class="listd-right">
<p><?php echo $review['review'];?></p>
<label class="date-year"><?php echo date('F Y',strtotime($review['dateAdded']));?></label>
</div>
<div class="listd-right">
<ul class="right-review">
<li><span style="padding-right:10px;">Your Rating</span><span class="review_img" ><img class="review_st" style="padding:10px 0; width:<?php echo $review['total_review'] * 20?>%"></span></li>
</ul>
</div>
</li>
<?php endforeach;}?>
</ul>

</div>

</div>
<!---DASHBOARD-->
<!---FOOTER-->
<script>
$(document).ready(function(){
    $('.exemple5').jRating({
        length:4.6,
        decimalLength:1,
        onSuccess : function(){
            alert('Success : your rate has been saved :)');
            //$("#rating_value_Err").hide();
        },
        onError : function(){
            alert('Error : please retry');
        }
    });
});


function add_review()
{
if($('#review-text').val()==''){$('#review-text').focus();return false;}
r1 = $('#r1').val();
$('#r7').val(r1);
$('#reviewForm').submit();
}


function post_discussion(evt)
{


$('#rental_id').val($(evt).attr('product_id'));
$('#sender_id').val($(evt).attr('reviewer_id'));
$('#receiver_id').val($(evt).attr('renter_id'));
$('#booking_id').val($(evt).attr('booking_no'));
}

$(function()
{
$('#discussion').click(function()
{
if($('#message').val()=='')
{
// $('#message_warn').html('Please Enter Message');
}
else{
$('#add_discussion').submit();
}
});
});


function add_data(evt)
{ 
	var product_id = $(evt).attr('product_id');
	var reviewer_id = $(evt).attr('reviewer_id');
	var booking_no = $(evt).attr('booking_no');
	var user_id = $(evt).attr('user_id');
	$('#add_review #user_id').val(user_id);
	$('#add_review #proid').val(product_id);
	$('#add_review #booking_no').val(booking_no);
}


function booking_review(evt){
	var booking_no = $(evt).attr('booking_no');
	var user_id = $(evt).attr('user_id');
	//alert(booking_no);
	//alert(user_id);
	$('#display_review ul li.mainli').hide();
	$('#display_review ul li.'+booking_no).show();
}


</script>
<!------------------pop up for inbox message--------------------->
<div id="inbox" class="modal in" style="overflow: hidden; display: none; height: 450px;" aria-hidden="false">
<div class="modal-header" style="background: none repeat scroll 0% 0% rgb(239, 239, 239); padding: 18px 10px 18px 40px;"><div ><b>Message to Host </b><a style="float:right; padding-right: 30px;cursor: pointer;"; data-dismiss="modal"><span class="">X</span></a></div></div>
<?php if($loginCheck !=''){ echo form_open('site/product/add_discussion',array('id'=>'add_discussion','style'=>'margin: 20px 20px 20px 40px'));?>
<input type="hidden" id="rental_id" name="rental_id" value="" />
<input type="hidden" id="sender_id" name="sender_id" value="" />
<input type="hidden" id="receiver_id" name="receiver_id" value="" />
<input type="hidden" id="booking_id" name="bookingno" value="" />
<input type="hidden" id="posted_by" name="posted_by" value="customer" />
<input type="hidden" id="redirect" name="redirect" value="<?php echo $this->uri->segment(1).'/'.$this->uri->segment(2);?>" />
<label></label>
<textarea  name="message" required id="message" class="scroll_newdes" style="height:90px; width: 440px;" ></textarea>
<div id="message_warn"  style="float:left; color:#FF0000;"></div>
<div class="clear"></div>
<div class="field_login" style=" margin-top:10px;">
<input type="submit" name="discussion" id="discussion"   style="float: right; background: none repeat scroll 0% 0% rgb(52, 129, 201); color: rgb(255, 255, 255); text-shadow: 0px 0px 0px rgb(255, 255, 255);" value="Send">
</div>
<?php echo form_close();}?>
</div>
<!------------------end popup for inbox message------------------->
<?php 
$this->load->view('site/templates/footer');
?>