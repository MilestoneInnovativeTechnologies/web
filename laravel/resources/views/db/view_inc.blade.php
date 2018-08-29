			<div class="row">
				<div class="col-md-6">
					<div class="table table-responsive"><table class="table table-striped"><tbody>
						<tr><th>Domain</th><th>:</th><td>{{ $Data->domain }}</td></tr>
						<tr><th>Distributor</th><th>:</th><td>{{ $Data->Distributor->name }}</td></tr>
						<tr><th colspan="3" style="background-color: #DDD;">&nbsp;</th></tr>@php $B = $Data->Branding; $LArray = ['name','heading','caption','about','address','email','number'] @endphp
						@foreach($LArray as $Key => $Val) <tr><th>{{ (is_int($Key))?ucwords($Val):$Val }}</th><th>:</th><td>{!! nl2br((is_int($Key))?($B->$Val):$B->$Key) !!}</td></tr> @endforeach
					</tbody></table></div>
				</div>
				<div class="col-md-6">
					<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>Icon</th><th>Theme Color</th></tr></thead><tbody>
						<tr><td width="50%"><div style="height: 130px; background-size: contain; background-repeat: no-repeat; background-position:left center; background-image: url({!! Storage::disk('branding')->url($B->icon) !!})"></div>
						</td><td><div style="height: 130px; background-color: rgba({{$B->color_scheme}},1)"></div></td></tr>
					</tbody></table></div>
					<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Product</th><th>Edition</th></tr></thead><tbody>
					@foreach($Data->Branding->Products as $Product)
						<tr><th>{{ $loop->iteration }}</th><td>{{ $Product->Product->name }}</td><td>{{ $Product->Edition->name }}</td></tr>
					@endforeach
					</tbody></table></div>
					<div class="table table-responsive"><table class="table table-bordered"><thead><tr><th>No</th><th>Name/FA Icon</th><th>Link</th><th>Target</th></tr></thead><tbody>
					@if($Data->Branding->Links->isNotEmpty()) @foreach($Data->Branding->Links as $Link)
						<tr><th>{{ $loop->iteration }}</th><td>{{ ($Link->name)?:$Link->fa }}</td><td>{{ $Link->link }}</td><td>{{ (['_blank' => 'New Window','_self' => 'Same Window'])[$Link->target] }}</td></tr>
					@endforeach @endif
					</tbody></table></div>
				</div>
			</div>
