<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6>Edit Slider</h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'editslider_form', 'enctype' => 'multipart/form-data');
						echo form_open_multipart('admin/slider/insertEditSlider',$attributes) 
					?>
	 						<ul>
	 							<li>
								<div class="form_grid_12">
									<label class="field_title" for="user_name">Slider Name </label>
									<div class="form_input">
										<input name="slider_name" style=" width:295px" id="slider_name" value="<?php echo $slider_details->row()->slider_name;?>" type="text" tabindex="1" class="required tipTop" title="Please enter the slider name"/>
									</div>
								</div>
								</li>
	 							<li>
								<div class="form_grid_12">
									<label class="field_title" for="full_name">Slider Title <span class="req">*</span></label>
									<div class="form_input">
										<input name="slider_title" style=" width:295px" id="slider_title" value="<?php echo $slider_details->row()->slider_title;?>" type="text" tabindex="1" class="required tipTop" title="Please enter the slider title"/>
									</div>
								</div>
								</li>
                                <li>
								<div class="form_grid_12">
									<label class="field_title" for="image">Slider Image<span class="req">*</span></label>
									<div class="form_input">
										<input name="image" id="image" type="file" tabindex="7" class="large tipTop" title="Please select slider image"/>
									</div>
									<div class="form_input"><img src="<?php echo base_url();?>images/slider/<?php echo $slider_details->row()->image;?>" width="100px"/><br>
									<span style="color:red;">Upload the Image Size 1349 X 484 or Above</span></div>
								
								</div>
								
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="slider_link">Slider Link <span class="req">*</span></label>
									<div class="form_input">
										<input name="slider_link" id="slider_link" type="text" value="<?php echo $slider_details->row()->slider_link;?>" tabindex="2" class="required large tipTop" title="Please enter the slider link"/>
									</div>
								</div>
								</li>
                                <li>
								<div class="form_grid_12">
									<label class="field_title" for="slider_desc">Slider Description <span class="req">*</span></label>
									<div class="form_input">
										<textarea name="slider_desc" id="slider_desc" class="required large tipTop" title="Please enter the slider link"><?php echo $slider_details->row()->slider_desc;?></textarea>
									</div>
								</div>
								</li>
								
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="admin_name">Status <span class="req">*</span></label>
									<div class="form_input">
										<div class="active_inactive">
											<input type="checkbox" name="status" <?php if ($slider_details->row()->status == 'Active'){echo 'checked="checked"';}?> id="active_inactive_active" class="active_inactive"/>
										</div>
									</div>
								</div>
								<input type="hidden" name="slider_id" value="<?php echo $slider_details->row()->id;?>"/>
								</li>
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="4"><span>Update</span></button>
									</div>
								</div>
								</li>
							</ul>
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
	</div>
</div>
<?php 
$this->load->view('admin/templates/footer.php');
?>