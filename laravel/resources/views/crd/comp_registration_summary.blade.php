@php
$Product = []; $Edition = []; $Summary = [];
if($Data->isNotEmpty()){
	foreach($Data as $Obj){
		if(!array_key_exists($Obj->product,$Product)) $Product[$Obj->product] = $Obj->Product->name;
		if(!array_key_exists($Obj->edition,$Edition)) $Edition[$Obj->edition] = $Obj->Edition->name;
		if(!array_key_exists($Obj->product,$Summary)) $Summary[$Obj->product] = [];
		if(!array_key_exists($Obj->edition,$Summary[$Obj->product])) $Summary[$Obj->product][$Obj->edition] = [];
		$Time = is_null($Obj->registered_on) ? strtotime(date('Y-m-d',strtotime($Obj->created_at))) : strtotime($Obj->registered_on);
		if(!array_key_exists($Time,$Summary[$Obj->product][$Obj->edition])) $Summary[$Obj->product][$Obj->edition][$Time] = [0,0];
		if($Obj->registered_on && $Obj->serialno && $Obj->key) $Summary[$Obj->product][$Obj->edition][$Time][0]++;
		else $Summary[$Obj->product][$Obj->edition][$Time][1]++;
	}
}
//dd($Summary);
$Class = 'rsp_' . mt_rand(11,99);
@endphp
<div class="table-responsive {{ $Class }}"><table class="table table-bordered">
	<thead><tr><th>Product</th><th>Edition</th><th>Registered</th><th>Unregistered</th><th>Total</th></tr></thead><tbody></tbody>
</table></div>
@push('js')
<script type="text/javascript">
$(function(){
	$('[name="reg_sum_period"]').on('change',function(){
		$Period = this.value+""; _cd = new Date(); $Till = parseInt((new Date(_cd.getFullYear(),_cd.getMonth()+1,1)).getTime()/1000); if($Period.indexOf("&")>-1) $Till = parseInt($Period.split('&')[1]); $Period = parseInt($Period.split('&')[0]); 
		$RegData = {!! json_encode($Summary) !!}; $Products = {!! json_encode($Product) !!}; $Editions = {!! json_encode($Edition) !!}; $Class = '{{ $Class }}';
		TBD = $('tbody',$('.{{ $Class }}')).empty();
		NT = [0,0];
		$.each($RegData,function(PRD,EDNObj){
			TR = $('<tr>').addClass('PRD_'+PRD).html($('<th>').attr({rowspan:Object.keys(EDNObj).length+1}).text($Products[PRD]).css({'border-bottom':'2px solid #DDD','vertical-align':'middle'})).appendTo(TBD);
			PT = [0,0];
			$.each(EDNObj,function(EDN,TimeObj){
				TR.addClass('EDN_'+EDN).append($('<td>').text($Editions[EDN]));
				Reg = 0; Unreg = 0;
				$.each(TimeObj,function(Time,RegArray){
					if(parseInt(Time) >= $Period && parseInt(Time) < $Till){
						Reg += parseInt(RegArray[0]); Unreg += parseInt(RegArray[1]);
						PT[0] += parseInt(RegArray[0]); PT[1] += parseInt(RegArray[1]);
						NT[0] += parseInt(RegArray[0]); NT[1] += parseInt(RegArray[1]);
					}
				});
				TR.append($('<td>').addClass('reg text-center').html(RDUrl(Reg,$Period,$Till,'reg',PRD,EDN))).append($('<td>').addClass('unreg text-center').html(RDUrl(Unreg,$Period,$Till,'unreg',PRD,EDN))).append($('<td>').addClass('Total text-center Total_'+PRD).html(RDUrl(Reg+Unreg,$Period,$Till,'',PRD,EDN)));
				TR = $('<tr>').addClass('PRD_'+PRD).appendTo(TBD);
			});
			TR.append($('<th>').text('Total')).append($('<td>').html(RDUrl(PT[0],$Period,$Till,'reg',PRD,'')).addClass('text-center')).append($('<td>').html(RDUrl(PT[1],$Period,$Till,'unreg',PRD,'')).addClass('text-center')).append($('<td>').html(RDUrl(PT[0]+PT[1],$Period,$Till,'',PRD,'')).addClass('text-center')).css({'border-bottom':'2px solid #DDD','font-weight':'bold'});
		});
		$('<tr>').html($('<th colspan="2">').text('Total')).append($('<td>').html(RDUrl(NT[0],$Period,$Till,'reg','','')).addClass('text-center')).append($('<td>').html(RDUrl(NT[1],$Period,$Till,'unreg','','')).addClass('text-center')).append($('<td>').html(RDUrl(NT[0]+NT[1],$Period,$Till,'','','')).addClass('text-center')).appendTo(TBD).css('font-weight','900');
	}).trigger('change')
});
function RDUrl(C,P,T,R,S,E){
	A = $('<a>').attr({ target:'_blank', href:RDHref(P,T,R,S,E) }).text(C).css({ color:'inherit' });
	return A[0];
}
function RDHref(P,T,R,S,E){
	return "{!! Route('reg.detail',['distributor' => (isset($Distributor) && $Distributor)?$Distributor:'', 'dealer' => (isset($Dealer) && $Dealer)?$Dealer:'','period' => '_period_','till' => '_till_','type' => '_type_','product' => '_product_','edition' => '_edition_']) !!}".replace('_period_',P).replace('_till_',T).replace('_type_',R).replace('_product_',S).replace('_edition_',E);
}
</script>
@endpush