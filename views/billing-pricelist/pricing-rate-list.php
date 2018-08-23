<?php
use yii\helpers\Html;
$rateType = implode(",",$rateType);
$displayrange = $rateType ==1?'style="display:none;"':'';
?>
<thead>
	<tr>
		<th class="sorting_disabled price-location-width" scope="col"><a href="javascript:void(0);" title="Location" class="tag-header-black">Location</a></th>
		<th class="sorting_disabled price-bill-width" scope="col"><a href="javascript:void(0);" title="Bill" class="tag-header-black">Bill</a></th>
		<th class="sorting_disabled price-cost-width" scope="col"><a href="javascript:void(0);" title="Cost" class="tag-header-black">Cost</a></th>
		<th class="sorting_disabled ifistiered price-range-width" <?= $displayrange ?> scope="col"><a href="javascript:void(0);" title="Range" class="tag-header-black">Range</a></th>
		<th class="sorting_disabled third-th" scope="col"><input type="hidden" name="rate_type_added" id="rate_type_added" value="<?=$rateType?>"/><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
	</tr>
</thead>	
<!--</tbody> -->
<tbody class="tbodyClass">
	<?php 
		if(!empty($pricingRates)){
			$i=1;
			foreach($pricingRates as $rate){
				$trClass = '';
				/*$trClass = 'even';
				if($i%2 == 0)
					$trClass = 'odd';*/
				$tier_from_tier_to = ''; 	
				if($rate->rate_type == 2)
					$tier_from_tier_to = $rate->tier_from.'-'.$rate->tier_to;
					
				echo '<tr class="',$trClass,' newTr">
					<td class="text-left skip-export kv-align-middle word-break price-location-width">
						',$rate->teamlocationMaster->team_location_name,'
						<input type="hidden" name="PricingRatesAvail[rate_type][]" class="rate_type" value="',$rate->rate_type,'"/>
						<input type="hidden" name="PricingRatesAvail[team_loc][]" class="team_loc" value="',$rate->team_loc,'"/>
					</td>
					<td class="skip-export kv-align-middle price-bill-width word-break">
						$',number_format($rate->rate_amount,2),'
						<input type="hidden" name="PricingRatesAvail[rate_amount][]" class="rate_amount" value="',number_format($rate->rate_amount,2,'.',''),'"/>
					</td>
					<td class="skip-export kv-align-middle price-cost-width word-break">
						$',$rate->cost_amount,'
						<input type="hidden" name="PricingRatesAvail[cost_amount][]" class="cost_amount" value="',$rate->cost_amount,'"/>
					</td>
					<td class="text-left skip-export kv-align-middle word-break ifistiered price-range-width" '.$displayrange.'>
						',$tier_from_tier_to,'
						<input type="hidden" name="PricingRatesAvail[tier_from][]" class="tier_from" value="',$rate->tier_from,'"/>
						<input type="hidden" name="PricingRatesAvail[tier_to][]" class="tier_to" value="',$rate->tier_to,'"/>
					</td>
					<td class="word-break skip-export kv-align-middle third-td text-center">
						<a class="icon-set removePricingRate" title="Remove Rate" href="javascript:void(0);" aria-label="Remove Rate">
						<em class="fa fa-close text-primary" title="Remove Rate"></em>
						</a>
					</td>
				</tr>';
				$i++;
			}
		} else {
			echo '<tr class="odd no-rows"><td colspan="4" style="text-align:center;">No Rates added yet</td></tr>';
		}
	?>
</tbody>
