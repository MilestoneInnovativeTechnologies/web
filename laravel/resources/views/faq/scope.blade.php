@extends("faq.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\FAQ::find(request()->id); $Scope = $Data->Scope; @endphp

<div class="content">
	<form method="post">
		{{ csrf_field() }}
		<div class="panel panel-default">
		<div class="panel-heading"><strong>Update Scopes</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('faq.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>
					<tr><th colspan="2">{!! nl2br($Data->question) !!}</th></tr>
					<tr><td colspan="2">{!! nl2br($Data->answer) !!}</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><th colspan="2">Scopes</th></tr>
					<tr><th colspan="2">
							<div class=""><label class="checkbox-inline"><input type="checkbox" name="public" value="public" @if($Scope && $Scope->public === 'YES') checked @endif> Public</label></div>
							<div class=""><label class="checkbox-inline"><input type="checkbox" name="support" value="support" @if($Scope && $Scope->support === 'YES') checked @endif> All Support Team</label></div>
							<div class=""><label class="checkbox-inline"><input type="checkbox" name="distributor" value="distributor" @if($Scope && $Scope->distributor === 'YES') checked @endif> All Distributor</label></div>
							<div class=""><label class="checkbox-inline"><input type="checkbox" name="customer" value="customer" @if($Scope && $Scope->customer === 'YES') checked @endif> All Customers</label></div>
							<div class="">
								<label class="checkbox-inline">
									<input type="checkbox" name="partner" value="{{ ($Scope) ? $Scope->partner : '' }}" @if($Scope && $Scope->partner) checked @endif>
									<input name="partner_name" onkeyup="name_changed()" type="text" class="form-control" placeholder="Partner" value="@if($Scope && $Scope->partner) {{ $Scope->Partner->name }} @endif">
								</label>
							</div>
						</th></tr>
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
        foreach($scope_keys as $key) if($Obj && $Obj->$key === 'YES') $scopes[] = ucfirst($key);
        if($Obj && $Obj->partner) $scopes[] = $Obj->Partner->name;
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