@extends("db.page")
@include('BladeFunctions')
@section("content")
@php $Products = \App\Models\Product::with(['Editions' => function($Q){ $Q->select('code','name'); }])->select('code','name')->get(); @endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Branding Distributor - Website Settings</strong>{!! PanelHeadBackButton((url()->previous() == url()->current())?Route('db.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-4">{!! formGroup(2,'distributor','select','Distributor',old('distributor'),['labelWidth' => 4, 'selectOptions' => \App\Models\Distributor::pluck('name','code')->toArray()]) !!}</div>
				<div class="col col-md-4">{!! formGroup(2,'domain','text','Site Domain',old('domain'), ['labelWidth' => 4, 'attr' => 'placeholder=\'sub.domain.com\' required']) !!}</div>
				<div class="col col-md-4">{!! formGroup(2,'type','select','Page nature',old('type'), ['labelWidth' => 4, 'selectOptions' => ['company' => 'Company','product' => 'Product']]) !!}</div>
			</div>
			<hr>
			<div class="row">
				<div class="col col-md-6">
					{!! formGroup(2,'name','text','Name',old('name'), ['labelWidth' => 4]) !!}
					{!! formGroup(2,'icon','file','Icon',old('icon'), ['labelWidth' => 4]) !!}
					{!! formGroup(2,'heading','text','Main Heading',old('heading'), ['labelWidth' => 4]) !!}
					{!! formGroup(2,'caption','text','Sub Heading',old('caption'), ['labelWidth' => 4]) !!}
					<div class="form-group clearfix form-horizontal">
						<label class="control-label col-xs-4">Color Scheme</label>
						<div class="col-xs-8">
							<div class="input-group">
								<span class="input-group-addon" style="padding: 0px; background-color: #FFF;"><input type="number" min="0" max="255" onChange="color_scheme_change()" class="form-control" placeholder="RED" name="cs[r]" value="{{ old('cs.r') }}" style="border-width:0px"></span>
								<span class="input-group-addon" style="padding: 0px; background-color: #FFF;"><input type="number" min="0" max="255" onChange="color_scheme_change()" class="form-control" placeholder="GREEN" name="cs[g]" value="{{ old('cs.g') }}" style="border-width:0px"></span>
								<span class="input-group-addon" style="padding: 0px; background-color: #FFF;"><input type="number" min="0" max="255" onChange="color_scheme_change()" class="form-control" placeholder="BLUE" name="cs[b]" value="{{ old('cs.b') }}" style="border-width:0px"></span>
								<span class="input-group-addon color_scheme" style="padding: 0px;"></span>
							</div>
						</div>
					</div>
				</div>
				<div class="col col-md-6">
					{!! formGroup(2,'about','textarea','About',old('about'), ['labelWidth' => 4]) !!}
					{!! formGroup(2,'address','textarea','Mail Address',old('address'), ['labelWidth' => 4]) !!}
					{!! formGroup(2,'email','textarea','Emails',old('email'), ['labelWidth' => 4]) !!}
					{!! formGroup(2,'number','textarea','Contact Numbers',old('number'), ['labelWidth' => 4]) !!}
				</div>
			</div><hr><div class="row">
				<div class="col col-md-6">
					<div class="table-responsive">
						<table class="table table-striped products"><caption><strong>Products Available</strong></caption>
							<thead><tr><th>Product</th><th>Edition</th><th width="40">&nbsp;</th></tr></thead>
							<tbody></tbody>
							<tfoot><tr><td><a href="javascript:AddProductLine()" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> &nbsp; New Line</a> </td><td colspan="4">&nbsp;</td></tr></tfoot>
						</table>
					</div>
				</div>
				<div class="col col-md-6">
					<div class="table-responsive">
						<table class="table table-striped links"><caption><strong>Social Media or External Links</strong></caption>
							<thead><tr><th>Link</th><th>Name</th><th>FA Icon</th><th>Target</th><th width="40">&nbsp;</th></tr></thead>
							<tbody></tbody>
							<tfoot><tr><td><a href="javascript:AddLinkLine()" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> &nbsp; New Line</a> </td><td colspan="4">&nbsp;</td></tr></tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" value="Save Settings" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection
@push('js')
<script type="text/javascript">
var _Products = {!! $Products->mapWithKeys(function($item){ return [ $item->code => [$item->name,$item->Editions->pluck('name','code')] ]; })->toJson() !!}
$(function(){
	color_scheme_change();
	@if(!is_null(old('product')) && !is_null(old('edition')))
		@php $products = old('product'); $edition = old('edition'); $Ary = []; foreach($products as $key => $prd) array_push($Ary,[$prd,$edition[$key]]); @endphp
		AddProducts({!! json_encode($Ary) !!});
	@endif
	@if(!is_null(old('link')) && !is_null(old('lname')) && !is_null(old('fa')))
		@php $links = old('link'); $names = old('lname'); $fas = old('fa'); $targets = old('target'); $Ary = []; foreach($links as $key => $link) array_push($Ary,[$link,$names[$key],$fas[$key],$targets[$key]]); @endphp
		AddLinks({!! json_encode($Ary) !!})
	@endif
})
</script>
<script type="text/javascript" src="js/brand_form.js"></script>
@endpush