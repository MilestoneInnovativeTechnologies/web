@extends("customer.page")
@include('BladeFunctions')
@section("content")


<div class="content customer_edit">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<form method="post">{{ csrf_field() }}
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Modify Details of {{ $Customer->name }}</strong>{!! PanelHeadBackButton((url()->previous() == url()->current()) ? Route('customer.index') : url()->previous()) !!}</div>
				<div class="panel-body">
					<div class="clearfix">
						<div class="col-xs-6">{!! formGroup(1,'name','text','Name',old('name',$Customer->name)) !!}</div>
						<div class="col-xs-6">{!! formGroup(1,'email','text','Email',old('email',$Customer->Logins[0]->email)) !!}</div>
					</div>
					<div class="clearfix">
						<div class="col-xs-6">{!! formGroup(1,'address1','text','Address Line 1',old('name',$Customer->Details->address1)) !!}</div>
						<div class="col-xs-6">{!! formGroup(1,'address2','text','Address Line 2',old('email',$Customer->Details->address2)) !!}</div>
					</div>
					<div class="clearfix">
						<div class="col-xs-4">{!! formGroup(1,'country','select','Country',old('country',($Customer->Details->city)?($Customer->Details->City->State->Country->id):null),['selectOptions' => $Customer->Parent1->ParentCountries->map(function($item){ return ['text' => $item->Country->name, 'value' => $item->Country->id, 'attr' => 'data-phonecode='.$item->Country->phonecode]; })->toArray()]) !!}</div>
						<div class="col-xs-4">{!! formGroup(1,'state','select','State',old('state',($Customer->Details->city)?($Customer->Details->City->State->id):null),['attr' => 'onChange=\'LoadStateCities()\'']) !!}</div>
						<div class="col-xs-4">{!! formGroup(1,'city','select','City',old('city',$Customer->Details->city)) !!}</div>
					</div>
					<div class="clearfix">
						<div class="col-xs-4">{!! formGroup(1,'industry','select','Indistry',old('industry',$Customer->Details->industry),['attr' => 'onChange=\'IndustryChanged()\'', 'selectOptions' => array_merge(['' => 'none'],\App\Models\CustomerIndustry::pluck('name','code')->toArray(),[['text' => 'Add New Industry','value' => '-1']])]) !!}
							<div class="new_industry_div" style="display: none;">
								<input type="text" class="form-control reduce_width" id="new_industry" name="new_industry" placeholder="New Industry Name" value="">
								<a href="javascript:NoNewIndustry()" title="Remove"><span class="glyphicon glyphicon-remove pull-right top_adjust"></span></a>
								<div class="clear"></div>
							</div>
						</div>
						<div class="col-xs-4">{!! formGroup(1,'phone','text','Phone',old('phone',$Customer->Details->phone),['inputGroup' => 'phonecode']) !!}</div>
						<div class="col-xs-4">{!! formGroup(1,'website','text','Website',old('website',$Customer->Details->website)) !!}</div>
					</div>
				</div>
				<div class="panel-footer clearfix">
					<input type="submit" name="submit" value="Update Details" class="btn btn-primary pull-right">
				</div>
			</div>
			</form>
		</div>
	</div>
	
</div>

@endsection
@push("css")
<style type="text/css">
	.reduce_width { width: calc(100% - 20px); float: left !important; }
	.top_adjust { top: 9px; }
</style>
@endpush