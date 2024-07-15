<div class="form-group">
	<select name="garage" data-size="5" class="selectpicker <?php houzez_ajax_search(); ?> form-control bs-select-hidden" title="<?php echo houzez_option('srh_garage', 'Garage'); ?>" data-live-search="false">
		<option value=""><?php echo houzez_option('srh_garage', 'Garage'); ?></option>
        <?php houzez_number_list('garage'); ?>
	</select><!-- selectpicker -->
</div>