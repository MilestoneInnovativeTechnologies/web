@extends("faq.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\FAQ::find(request()->id); $Products = $Data->Products; $Checks = $Products->map(function($item){ return implode('-',[$item->product,$item->edition]); }); @endphp
@php $Prods = \App\Models\Product::where('active',1)->with(['Editions' => function($Q){ $Q->where('active',1); }])->get(); @endphp

<div class="content">
	<form method="post">
		{{ csrf_field() }}
		<div class="panel panel-default">
		<div class="panel-heading"><strong>Update Software</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('faq.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>
					<tr><th colspan="2">{!! nl2br($Data->question) !!}</th></tr>
					<tr><td colspan="2">{!! nl2br($Data->answer) !!}</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><th>Software</th><th>Editions</th></tr>
					@foreach($Prods as $Prod)
						<tr>
							<td><label class="checkbox-inline"><input type="checkbox" name="product[]" value="{{ $Prod->code }}" @if($Products->contains('product',$Prod->code)) checked @endif> {{ $Prod->name }}</label></td>
							<td>
								<div class="col-xs-3" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="edition[{{ $Prod->code }}][All]" value="All" onchange="AllEditions('{{ $Prod->code }}')" @if($Checks->contains($Prod->code.'-')) checked @endif> All Editions</label></div>
								@foreach($Prod->Editions as $Edition)
									<div class="col-xs-3" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="edition[{{ $Prod->code }}][]" value="{{ $Edition->code }}" @if($Checks->contains($Prod->code.'-'.$Edition->code)) checked @endif> {{  $Edition->name }}</label></div>
								@endforeach
							</td>
						</tr>
					@endforeach
					</tbody></table></div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Update Scopes" class="btn pull-right btn-primary">
		</div>
	</div>
	</form>
</div>

@endsection
@php
	function Scopes($Obj){
        $scope_keys = ['public','support','distributor','customer'];
        $scopes = [];
        foreach($scope_keys as $key) if($Obj->$key === 'YES') $scopes[] = ucfirst($key);
        if($Obj->partner) $scopes[] = $Obj->Partner->name;
        return implode(", ", $scopes);
    }

@endphp
@push('js')
	<script type="text/javascript">
        function name_changed(){
            $('[name="partner"]').prop('checked',$('[name="partner_name"]').val() !== "");
        }
        function SetPartner(itm){
            $('[name="partner_name"]').val(itm.name);
            $('[name="partner"]').val(itm.code);
            return false;
        }
        function AllEditions(prd){
            ENA = $('input[type="checkbox"][name="edition['+prd+'][All]"]');
            EA = $('input[type="checkbox"][name="edition['+prd+'][]"]');
            EA.prop('checked',ENA.prop('checked'));
        }
        $(function(){
            $('[name="partner_name"]').autocomplete({
                minLength: 1,
                source: '/api/v1/faq/get/prt',
                select: function(event, ui){ return SetPartner(ui.item); },
                focus: function(event, ui){ return SetPartner(ui.item); }
            }).autocomplete( "instance" )._renderItem = function(ul, item) {
                return $( "<li>" ).appendTo( ul ).append( "<div>" + item.name + "</div>" );
            };

        });
	</script>
@endpush