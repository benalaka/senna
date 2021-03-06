<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('recvs_register'); ?></div>

<link rel="stylesheet" type="text/css" href="css/dataTables.min.css" />
<script src="<?php echo base_url();?>js/dataTables.min.js"></script>

<?php
// echo "<pre>";print_r($cart);echo "</pre>";
if(isset($error))
{
	echo "<div class='error_message'>".$error."</div>";
}
?>
<div id="new_main1">
	<div id="register_wrapper" >
		<?php echo form_open("receivings/change_mode",array('id'=>'mode_form')); ?>
		<span><?php echo $this->lang->line('recvs_mode') ?></span>
		<?php echo form_dropdown('mode',$modes,$mode,'onchange="$(\'#mode_form\').submit();"'); ?>
	
		<?php 
		if ($show_stock_locations) 
		{	?>
	    	<span><?php echo $this->lang->line('recvs_stock_source') ?></span>
		    <?php echo form_dropdown('stock_source',$stock_locations,$stock_source,'onchange="$(\'#mode_form\').submit();"'); ?>
		    <?php 
		    if($mode=='requisition')
		    { ?>
		    	<span><?php echo $this->lang->line('recvs_stock_destination') ?></span>
				<?php echo form_dropdown('stock_destination',$stock_locations,$stock_destination,'onchange="$(\'#mode_form\').submit();"');        
		    }
		} ?>    
		</form>

		<?php echo form_open("receivings/add",array('id'=>'add_item_form')); ?>
		<label id="item_label" for="item">
		<?php 
		if($mode=='receive' or $mode=='requisition')
		{	
			echo $this->lang->line('recvs_find_or_scan_item');
		}else{
			echo $this->lang->line('recvs_find_or_scan_item_or_receipt');
		}?>
		</label>
		<?php echo form_input(array('name'=>'item','id'=>'item','size'=>'40'));?>
		<div id="new_item_button_register" >
			<?php echo anchor("items/view/-1/width:450","<div class='small_button'><span>".$this->lang->line('sales_new_item')."</span></div>",array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_item')));	?>
			
		</div>
		</form>

		<table id="register" class="stripe">
			<thead>
				<tr>
					<th style="width:9%;"><?php echo $this->lang->line('common_delete'); ?></th>
					<th style="width:30%;"><?php echo $this->lang->line('recvs_item_name'); ?></th>
					<th style="width:10%;"><?php echo $this->lang->line('recvs_cost'); ?></th>
					<th style="width:5%;"><?php echo $this->lang->line('recvs_quantity'); ?></th>
					<th style="width:11%;"></th>
					<th style="width:8%;"><?php echo $this->lang->line('recvs_discount'); ?></th>
					<th style="width:8%;">VAT</th>
					<th style="width:11%;">Expiry Date (m/d/Y)</th>
					<th style="width:14%;"><?php echo $this->lang->line('recvs_total'); ?></th>
					<th style="width:11%;"><?php echo $this->lang->line('recvs_edit'); ?></th>
				</tr>
			</thead>
			<tbody id="cart_contents">
			<?php 
				if(count($cart)==0)
				{?>
				<tr>
					<!-- <td colspan='12'> -->
						<!-- <div class='warning_message' style='padding:7px;'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div> -->
					<!-- </td> -->
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr><?php 
				}else{
				foreach(array_reverse($cart, true) as $line=>$item){					
        			echo form_open("receivings/edit_item/$line",array('id'=>'edit_item_form_'.$line));
        		?>
        		<tr>
        			<td><?php echo anchor("receivings/delete_item/$line",'['.$this->lang->line('common_delete').']');?></td>
        			<td style="align:center;"><?php echo $item['name']; ?><br /> [<?php echo $item['in_stock']; ?> in <?php echo $item['stock_name']; ?>]<?php echo form_hidden('location', $item['item_location']); ?>
        			</td>
        			<?php if ($items_module_allowed && $mode !='requisition'){?>
					<td><?php echo form_input(array('name'=>'price','value'=>$item['price'],'size'=>'6'));?></td>
					<?php 
							}else{?>
					<td><?php echo $item['price']; ?></td>
					<?php echo form_hidden('price',$item['price']); ?>
					<?php }	?>
		
					<td><?php echo form_input(array('name'=>'quantity','value'=>$item['quantity'],'size'=>'2'));
						if ($item['receiving_quantity'] > 1){?>
					</td>
					<td>x <?php echo $item['receiving_quantity']; ?></td>
					<?php }else	{?>
					<td></td><?php }?>
		
					<?php if ($items_module_allowed && $mode!='requisition') {?>
	    			<td><?php echo form_input(array('name'=>'discount','value'=>$item['discount'],'size'=>'3'));?></td>
	    			<?php  }else{?>
		     		<td><?php echo $item['discount']; ?></td>
		     		<?php echo form_hidden('discount',$item['discount']); ?><?php }	
		     			$new_value = 1;
		     			$cost = ($item['price']);
		     			$vat = ($item['vat']);
		     			$quantity = ($item['quantity']);
						$discount = ($item['discount']);
						$totalsfinal = 0;
						$receiving_quantity = ($item['receiving_quantity']);						
						$receiving_quantity = ($receiving_quantity==0) ? 1 : $receiving_quantity ;
						$totalcost = $cost*$quantity*$receiving_quantity;
						$totalvat = $totalcost*($vat/100);						
						$totalsfinal_vat = $totalcost + $totalvat;
						if($discount==0){
							$totaldiscount = 0;
						}else{
							$totaldiscount = $totalsfinal_vat * ($discount/100);		
						}
						$totalsfinal = $totalsfinal_vat - $totaldiscount;								
					?>
			
		 			<td><?php echo form_input(array('name'=>'vat','size'=>'5','value'=>$item['vat']));?></td>
					<td><?php echo form_input(array('name'=>'expiry','size'=>'5','class'=>'expiry_datepicker','value'=>$item['expiry']));?></td>			
					<td><?php echo to_currency($totalsfinal); ?></td>			 					
					<td><?php echo form_submit(array('name'=>'edit_item','class'=>'edits','value'=>$this->lang->line('sales_edit_item')));?></td>
					<!-- <td><?php //echo form_submit("edit_item", $this->lang->line('sales_edit_item'));?></td> -->
				</tr>			
				</form>
				<?php
			} }
			?>
			</tbody>



		</table>









	</div>


	<div id="overall_sale" >
	<?php
		if(isset($supplier))
		{
			echo $this->lang->line("recvs_supplier").': <b>'.$supplier. '</b><br />';
			echo anchor("receivings/delete_supplier",'['.$this->lang->line('common_delete').' '.$this->lang->line('suppliers_supplier').']');
		}
		else
		{
			echo form_open("receivings/select_supplier",array('id'=>'select_supplier_form')); ?>
			<label id="supplier_label" for="supplier"><?php echo $this->lang->line('recvs_select_supplier'); ?></label>
			<?php echo form_input(array('name'=>'supplier','id'=>'supplier','size'=>'30','value'=>$this->lang->line('recvs_start_typing_supplier_name')));?>
			</form>
			<div style="margin-top:5px;text-align:center;">
			<h3 style="margin: 5px 0 5px 0"><?php echo $this->lang->line('common_or'); ?></h3>
			<?php echo anchor("suppliers/view/-1/width:400",
			"<div class='small_button' style='margin:0 auto;'><span>".$this->lang->line('recvs_new_supplier')."</span></div>",
			array('class'=>'thickbox none','title'=>$this->lang->line('recvs_new_supplier')));
			?>
			</div>
			<div class="clearfix">&nbsp;</div>
			<?php
		}
		
		if($mode != 'requisition')
		{        	
	    ?>
		<div id='sale_details'>
			<div class="float_left" style='width:55%;'><?php echo $this->lang->line('sales_total'); ?>:</div>
			<div class="float_left" style="width:45%;font-weight:bold;">
			<?php 
				$total = 0;
				// echo "<pre>";print_r($cart);"</pre>";
				if(count($cart)>0){
					foreach ($cart as $item) {
						$new_value = 1;
		     			$cost = ($item['price']);
		     			$vat = ($item['vat']);
		     			$quantity = ($item['quantity']);
						$discount = ($item['discount']);
						$totalsfinal = 0;
						$receiving_quantity = ($item['receiving_quantity']);						
						$receiving_quantity = ($receiving_quantity==0) ? 1 : $receiving_quantity ;
						$totalcost = $cost*$quantity*$receiving_quantity;
						$totalvat = $totalcost*($vat/100);						
						$totalsfinal_vat = $totalcost + $totalvat;
						if($discount==0){
							$totaldiscount = 0;
						}else{
							$totaldiscount = $totalsfinal_vat * ($discount/100);		
						}
						$totalsfinal = $totalsfinal_vat - $totaldiscount;								
						$total+=$totalsfinal;
					}
				}
				echo to_currency($total); 
			?></div>
		</div>
		<?php 
	        }
	        if(count($cart) > 0)
			{
				if($mode == 'requisition')
				{
				?>
				    
				    <div  style='border-top:2px solid #000;' />
				    <div id="finish_sale">
				        <?php echo form_open("receivings/requisition_complete",array('id'=>'finish_receiving_form')); ?>
				        <br />
				        <label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?>:</label>
				        <?php echo form_textarea(array('name'=>'comment','id'=>'comment','value'=>$comment,'rows'=>'4','cols'=>'23'));?>
				        <br /><br />
				        
				        <div class='small_button' id='finish_receiving_button' style='float:right;margin-top:5px;'>
				        	<span><?php echo $this->lang->line('recvs_complete_receiving') ?></span>
				        </div>
				        </form>    
				        <?php echo form_open("receivings/cancel_receiving",array('id'=>'cancel_receiving_form')); ?>
				        <div class='small_button' id='cancel_receiving_button' style='float:left;margin-top:5px;'>
				        <span><?php echo $this->lang->line('recvs_cancel_receiving')?></span>
				        </div>
				        </form>
				     </div>
			    <?php
			        }else
			        {?>
					<div id="finish_sale">
						<?php echo form_open("receivings/complete",array('id'=>'finish_receiving_form')); ?>
						<br />
						<label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?>:</label>
						<?php echo form_textarea(array('name'=>'comment','id'=>'comment','value'=>$comment,'rows'=>'4','cols'=>'23'));?>
						<br /><br />
						<table width="100%">
							<tr>
								<td><?php echo $this->lang->line('recvs_print_after_sale'); ?></td>
								<td><?php if ($print_after_sale)
									{
										echo form_checkbox(array('name'=>'recv_print_after_sale','id'=>'recv_print_after_sale','checked'=>'checked'));
									}else{
										echo form_checkbox(array('name'=>'recv_print_after_sale','id'=>'recv_print_after_sale'));
										}?>										
								</td>
							</tr>
							<?php if ($mode == "receive"){?>
							<tr>
								<td><?php echo $this->lang->line('recvs_invoice_enable'); ?></td>
								<td><?php if ($invoice_number_enabled){	
											echo form_checkbox(array('name'=>'recv_invoice_enable','id'=>'recv_invoice_enable','size'=>10,'checked'=>'checked'));
										  }else{
											echo form_checkbox(array('name'=>'recv_invoice_enable','id'=>'recv_invoice_enable','size'=>10));
										}?>
								</td>
							</tr>
							<tr>
								<td><?php echo $this->lang->line('recvs_invoice_number').':   ';?></td>
								<td><?php echo form_input(array('name'=>'recv_invoice_number','id'=>'recv_invoice_number','value'=>$invoice_number,'size'=>10));?></td>
							</tr>
							<?php }	?>
							<tr>
								<td><?php echo $this->lang->line('sales_payment').':   ';?></td>
								<td><?php echo form_dropdown('payment_type',$payment_options);?></td>
	        				</tr>
	        				<tr>
	        					<td><?php echo $this->lang->line('sales_amount_tendered').':   ';?></td>
	        					<td><?php echo form_input(array('name'=>'amount_tendered','value'=>'','size'=>'10'));?></td>
	        				</tr>
	        			</table>
	        			<br />
						<div class='small_button' id='finish_receiving_button' style='float:right;margin-top:5px;'>
							<span><?php echo $this->lang->line('recvs_complete_receiving') ?></span>
						</div>
	        
						</form>

		   				<?php echo form_open("receivings/cancel_receiving",array('id'=>'cancel_receiving_form')); ?>
				    	<div class='small_button' id='cancel_receiving_button' style='float:left;margin-top:5px;'>
							<span><?php echo $this->lang->line('recvs_cancel_receiving')?></span>
						</div>
	       				</form>
				</div>
				<?php 
				}
			}
		?>

	</div>
</div>




<div class="clearfix" style="margin-bottom:30px;">&nbsp;</div>
<script type="text/javascript" language="javascript">
$(document).ready(function()
{
	$(".expiry_datepicker" ).datepicker();
    $("#item").autocomplete('<?php echo site_url("receivings/item_search"); ?>',
    {
    	minChars:0,
    	max:100,
       	delay:10,
       	selectFirst: false,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#item").result(function(event, data, formatted)
    {
		$("#add_item_form").submit();
    });

    $('#item').focus();

	$('#item').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

	$('#comment').keyup(function() 
	{
		$.post('<?php echo site_url("receivings/set_comment");?>', {comment: $('#comment').val()});
	});

	$('#recv_invoice_number').keyup(function() 
	{
		$.post('<?php echo site_url("receivings/set_invoice_number");?>', {recv_invoice_number: $('#recv_invoice_number').val()});
	});

	$("#recv_print_after_sale").change(function()
	{
		$.post('<?php echo site_url("receivings/set_print_after_sale");?>', {recv_print_after_sale: $(this).is(":checked")});
	});

	var enable_invoice_number = function() 
	{
		var enabled = $("#recv_invoice_enable").is(":checked");
		$("#recv_invoice_number").prop("disabled", !enabled).parents('tr').show();
		return enabled;
	}

	enable_invoice_number();

	$("#recv_invoice_enable").change(function() {
		var enabled = enable_invoice_number();
		$.post('<?php echo site_url("receivings/set_invoice_number_enabled");?>', {recv_invoice_number_enabled: enabled});
		
	});

	$('#item,#supplier').click(function()
    {
    	$(this).attr('value','');
    });

    $("#supplier").autocomplete('<?php echo site_url("receivings/supplier_search"); ?>',
    {
    	minChars:0,
    	delay:10,
    	max:100,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#supplier").result(function(event, data, formatted)
    {
		$("#select_supplier_form").submit();
    });

    $('#supplier').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('recvs_start_typing_supplier_name'); ?>");
    });

    $("#finish_receiving_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("recvs_confirm_finish_receiving"); ?>'))
    	{
    		$('#finish_receiving_form').submit();
    	}
    });

    $("#cancel_receiving_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("recvs_confirm_cancel_receiving"); ?>'))
    	{
    		$('#cancel_receiving_form').submit();
    	}
    });


});

function post_item_form_submit(response, stay_open)
{
	if(response.success)
	{
		$("#item").attr("value",response.item_id);
		if (stay_open)
		{
			$("#add_item_form").ajaxSubmit();
		}
		else
		{
			$("#add_item_form").submit();
		}
	}
}

function post_person_form_submit(response)
{
	if(response.success)
	{
		$("#supplier").attr("value",response.person_id);
		$("#select_supplier_form").submit();
	}
}

</script>
<script type="text/javascript">
	$(document).ready(function(){
		// $('#register').DataTable( {
		// 	 "oLanguage": {
		//         "sEmptyTable":     "My Custom Message On Empty Table"
		//     },
	 //        "paging":   false,
	 //        "ordering": false,
	 //        "info":     false,
	 //        "searching":false
	 //    });	
	 //    $(".dataTables_wrapper").css("width","100%");
	 //    $("#register").on("click", ".edits", function(){
		//    // your code as is
		//    console.log(e);
		//    // $( "edit_item_form" ).submit();
		// });
	});
   
</script>

<?php $this->load->view("partial/footer"); ?>