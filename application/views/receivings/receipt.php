<?php $this->load->view("partial/header"); ?>

<?php $this->load->view('partial/print_receipt', array('print_after_sale', $print_after_sale, 
		'selected_printer' => 'receipt_printer')); ?>

<?php
if (isset($error_message))
{
	echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
	exit;
}
?>
<div id="receipt_wrapper">
	<div id="receipt_header">
		<?php if ($this->Appconfig->get('company_logo') == '') 
        { 
        ?>
        <div id="company_name"><?php echo $this->config->item('company'); ?></div>
		<?php 
		}
		else 
		{ 
		?>
		
		<div id="company_name"><img id="image" src="<?php echo base_url('uploads/' . $this->Appconfig->get('company_logo')); ?>" alt="company_logo" /></div>			
		<?php
		}
		?>
		<div id="company_address"><?php echo nl2br($this->config->item('address')); ?></div>
		<div id="company_phone"><?php echo $this->config->item('phone'); ?></div>
		<div id="sale_receipt">Receivings Invoice</div>
		<div id="sale_time"><?php echo $transaction_time ?></div>
	</div>
	<div id="receipt_general_info">
		<?php if(isset($supplier))
		{
		?>
		<div id="customer"><?php echo $this->lang->line('suppliers_supplier').": ".$supplier; ?></div>
		<?php
		}
		?>
		<div id="sale_id"><?php echo $this->lang->line('recvs_id').": ".$receiving_id; ?></div>
		<?php if (!empty($invoice_number))
		{
		?>
		<div id="invoice_number"><?php echo $this->lang->line('recvs_invoice_number').": ".$invoice_number; ?></div>	
		<?php 
		}
		?>
		<div id="employee"><?php echo $this->lang->line('employees_employee').": ".$employee; ?></div>
	</div>

	<table id="receipt_items">
	<tr>
	<th style="width:30%;"><?php echo $this->lang->line('items_item'); ?></th>
	<th style="width:10%;"><?php echo $this->lang->line('common_price'); ?></th>
	<th style="width:20%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
	<th style="width:30%;"><?php echo $this->lang->line('Expiry').'(mm/dd/yyyy)'; ?></th>
	<th style="width:20%;">Discount</th>
	<th style="width:20%;">VAT</th>
		<th style="width:15%;text-align:right;"><?php echo $this->lang->line('sales_total'); ?></th>
	</tr>
	<?php
	foreach(array_reverse($cart, true) as $line=>$item)
	{
	?>
		<tr>
		<td><span class='long_name'><?php echo $item['name']; ?></span><span class='short_name'><?php echo character_limiter($item['name'],10); ?></span></td>
		
		<td><?php echo to_currency($item['price']); ?></td>
		<td><?php echo $item['quantity'];?>x <?php echo $item['receiving_quantity'] != 0 ? $item['receiving_quantity'] : 1; ?></td>
		<td><?php echo $item['expiry'];?></td>
		<td> <?php echo number_format($item['discount'], 0) ?> %</td>
		<td><?php echo $item['vat'];?> %</td>
		<!-- <td><?php //foreach($taxes as $name=>$value){ echo substr($name,0,6) ?></td> -->
		<td>
		<div class="total-value"><?php 
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
			echo to_currency(round($totalsfinal,2));
		// echo to_currency(round(($item['price']*$item['quantity']*$item['receiving_quantity']-$item['price']*$item['quantity']*$item['discount']*$item['receiving_quantity']/100+$item['vat']),0));//} ?></div></td>
		</tr>
	   <!--  <tr>
		<td ><?php //echo $item['serialnumber']; ?></td>
	    </tr> -->
	  
	
	<?php
	}
	?>	
	<tr>
	<td colspan="5" style='text-align:right;border-top:2px solid #000000;'><?php echo $this->lang->line('sales_total'); ?></td>
	<td style='border-top:2px solid #000000;'><div class="total-value">
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
				$amount_change =  $amount_tendered - $total; 
			?>
		<?php //echo to_currency($total); ?>
			
		</div></td>
	</tr>
	<?php 
    if($mode!='requisition')
    {
    ?>
	<tr>
	<td colspan="3" style='text-align:right;'><?php echo $this->lang->line('sales_payment'); ?></td>
	<td><div class="total-value"><?php echo $payment_type; ?></div></td>
	</tr>
	

	<?php if(isset($amount_change))
	{
	?>
		<tr>
		<td colspan="3" style='text-align:right;'><?php echo $this->lang->line('sales_amount_tendered'); ?></td>
		<td><div class="total-value"><?php echo to_currency($amount_tendered); ?></div></td>
		</tr>

		<tr>
		<td colspan="3" style='text-align:right;'><?php echo $this->lang->line('sales_change_due'); ?></td>
		<td><div class="total-value"><?php echo round($amount_change,2); ?></div></td>
		</tr>
	<?php
	}
	?>
	<?php 
    }
    ?>
	</table>

	<div id="sale_return_policy">
		<?php echo nl2br($this->config->item('return_policy')); ?>
	</div>
	<div id='barcode'>
		<img src='data:image/png;base64,<?php echo $barcode; ?>' /><br>
		<?php echo $receiving_id; ?>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>
