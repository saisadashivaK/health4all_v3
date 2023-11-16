<link rel="stylesheet" href="<?php echo base_url();?>assets/css/metallic.css" >
<script type="text/javascript" src="<?php echo base_url();?>assets/js/zebra_datepicker.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/theme.default.css" >
<style>
	.non_editable_field {
		
		border-radius: 3%;
		padding-top:2%;
		padding-bottom: 2%;
		background-color: #F0F0F0;
	}
	.label_sp {
		padding-left: 1%;
	}
</style>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/zebra_datepicker.js"></script>
<script type="text/javascript">
$(function(){
	$("#mfg_date").Zebra_DatePicker({
		direction:[false, '<?= date('d-M-Y', min($item->expiry_date ? strtotime($item->expiry_date): PHP_INT_MAX, strtotime(date('d-M-Y')))); ?>'], 
		view: 'years'
	});
	$("#expiry_date").Zebra_DatePicker({
		direction:[<?php echo $item->mfg_date ? "'".date('d-M-Y', strtotime($item->mfg_date))."'": false;?>, false], 
		view: 'years'
	});
	$('#edit').submit((e) => {
		// e.preventDefault();
		// console.log(e.target);
		const batch = $('#batch') ? $('#batch').val() : 'batchset';
		const mfg_date = $('#mfg_date') ? $('#mfg_date').val() : 'mfgdset';
		const exp_date = $('#expiry_date') ? $('#expiry_date').val() : 'expdset';
		const cost = $('#cost') ? $('#cost').val() : 'costset';
		const patient_id = $('#patient_id') ? $('#patient_id').val() : 'patientidset';
		const gtin_code = $('#gtin_code') ? $('#gtin_code').val() : 'gtincodeset';
		const note = $('#note') ? $('#note').val() : 'noteset';
		const inventory_id = $('#inventory_id').val();
		console.log(inventory_id, batch, mfg_date, exp_date, cost, patient_id, gtin_code, note);
	});
		
});
</script>
	<center>
		<?php 
		if(!$this->input->post('navigate_edit')){
			echo validation_errors('<div class="alert alert-danger">', '</div>');
		}
		if(isset($msg)){  ?>
	<div class="alert alert-info"><?php echo $msg; ?></div><?php } ?>
	<h3>Edit Inventory Item</h3></center><br>
	<?php echo form_open("consumables/inventory_item/edit/$item->inventory_id",array('class'=>'form-group','role'=>'form','id'=>'edit')); ?>
		
	
		<div class="col-md-6 col-md-offset-3">
			<div class="row">
				<!-- <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<h4>Inventory ID: <?= $item->inventory_id; ?> <input type="hidden" class="sr-only" name="inventory_id" id="inventory_id" value="<?= $item->inventory_id;?>"></h4>
					<br>
				</div> -->
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">					
					<div class="form-group">
						<label for="batch" class="col-md-12 col-lg-12 label_sp">Inventory ID</label>
						<p class='col-md-12 col-lg-12'><b><?= $item->inventory_id; ?></b></p>
						<input type="hidden" class="sr-only" name="inventory_id" id="inventory_id" value="<?= $item->inventory_id;?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">			
					<div class="form-group">
						<label for="indent_id" class="col-md-12 col-lg-12 label_sp">Indent ID</label>
						<p class='col-md-12 col-lg-12'><b><?= $item->indent_id; ?></b></p>
					</div>
				</div>														
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">					
					<div class="form-group">
						<label for="item_name" class="col-md-12 col-lg-12 label_sp">Item</label>
						<p class='col-md-12 col-lg-12'><b><?= $item->item_name; ?></b></p>
					</div>		
				</div>																
		
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">					
					<div class="form-group">
						<label for="batch" class="col-md-12 col-lg-12 label_sp">Batch Number</label>
						<?= isset($item->batch) && strlen($item->batch) > 0 ? "<p class='non_editable_field col-md-12 col-lg-12'>$item->batch</p>": "<input id='batch' name='batch' type='text' class='form-control' placeholder='Batch ID' maxlength='10' pattern='[0-9a-zA-Z]*'/>";?>
					</div>
				</div>																
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">					
					<div class="form-group">
						<label for="mfg_date" class="col-md-12 col-lg-12 label_sp">Manufacturing date</label>
						<?php
							$currdate = date("d-M-Y");
							$mfg_date = date("d-M-Y", strtotime($item->mfg_date));
							$null_date = date("d-M-Y", strtotime(0));
						?>
						<!-- <input id='mfg_date' name='mfg_date' class='form-control' type='text' value='16-Nov-2023' size='10' /> -->
						<?= $mfg_date !== $null_date ? "<p class='non_editable_field col-md-12 col-lg-12'>$mfg_date</p>": "<input id='mfg_date' name='mfg_date' class='form-control' type='text' value='16-Nov-2023' size='10' />"; ?>

					</div>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">					
					<div class="form-group">
						<label for="expiry_date" class="col-md-12 col-lg-12 label_sp">Expiry date</label>
						<?php
							$currdate = date("d-M-Y");
							$exp_date = date("d-M-Y", strtotime($item->expiry_date));
							$null_date = date("d-M-Y", strtotime(0));
						?>
						<?= $exp_date !== $null_date ? "<p class='non_editable_field col-md-12 col-lg-12'>$exp_date</p>": "<input id='expiry_date' name='expiry_date' class='form-control' type='text' value='' />"; ?>
					</div>
				</div>	
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="cost" class="col-md-12 col-lg-12 label_sp">Cost</label>
						<?= isset($item->cost) ? "<p class='non_editable_field col-md-12 col-lg-12'>Rs. $item->cost</p>": "<input id='cost' name='cost' class='form-control' type='text' placeholder='Cost'/>"; ?>
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="patient_id" class="col-md-12 col-lg-12 label_sp">Patient ID</label>
						<?= $item->patient_id && strlen($item->patient_id) > 0 ? "<p class='non_editable_field col-md-12 col-lg-12'>$item->patient_id</p>": "<input type='text' name=
						'patient_id' id = 'patient_id' class='form-control' placeholder='Patient ID' />";?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="gtin_code" class="col-md-6 col-lg-6 label_sp">GTIN Code</label>
						<?= $item->gtin_code && strlen($item->gtin_code) > 0 ? "<p class='non_editable_field col-md-12 col-lg-12'>$item->gtin_code</p>": "<input type='text' name='gtin_code' id='gtin_code' class='form-control' placeholder='Barcode no.' minlength='8' maxlength='14' pattern='[0-9]*' />"; ?>
					</div>
				</div>
				
				<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
					<div class="form-group">
						<label for="note" class="col-md-6 col-lg-6 label_sp">Note</label>
						<?= $item->note && strlen($item->note) > 0 ? "<p class='non_editable_field col-md-12 col-lg-12'>$item->note</p>" : "<textarea name='note' id='note' class='form-control' placeholder='Note' maxlength='2000'></textarea>";?>
					</div>
				</div>
			</div>

			<div class="col-md-3 col-md-offset-5">
				<button class="btn btn-primary " type="submit" value="submit">Submit</button><!--Submit button-->
			</div><!--Submit button end-->
	<?php echo form_close(); ?>
</div>
