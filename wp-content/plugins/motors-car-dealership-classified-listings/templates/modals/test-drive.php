<div class="modal" id="test-drive" tabindex="-1" role="dialog" aria-labelledby="myModalLabelTestDrive">
	<form id="request-test-drive-form" action="<?php echo esc_url( home_url('/') ); ?>" method="post">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header modal-header-iconed">
					<i class="icon-steering_wheel"></i>
					<h3 class="modal-title" id="myModalLabelTestDrive"><?php esc_html_e('Schedule a Test Drive', 'motors') ?></h3>
					<div class="test-drive-car-name"><?php echo stm_generate_title_from_slugs(get_the_id()); ?></div>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Name', 'motors'); ?></div>
								<input name="name" type="text"/>
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Email', 'motors'); ?></div>
								<input name="email" type="email" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Phone', 'motors'); ?></div>
								<input name="phone" type="tel" />
							</div>
						</div>
						<div class="col-md-6 col-sm-6">
							<div class="form-group">
								<div class="form-modal-label"><?php esc_html_e('Best time', 'motors'); ?></div>
								<div class="stm-datepicker-input-icon">
									<input name="date" class="stm-date-timepicker" type="text" />
								</div>
							</div>
						</div>
					</div>
					<div class="mg-bt-25px"></div>
					<div class="row">
						<div class="col-md-8 col-sm-8 msg-body">
							<input name="vehicle_id" type="hidden" value="<?php echo esc_attr(get_the_id()); ?>" />
						</div>
						<div class="col-md-4 col-sm-4">
							<button type="submit" class="stm-request-test-drive"><?php esc_html_e("Request", 'motors'); ?></button>
							<div class="stm-ajax-loader" style="margin-top:10px;">
								<i class="stm-icon-load1"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>