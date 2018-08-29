@extends("features.page")
@section("content")

<div class="content">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8"><form method="post" action="features">{{ csrf_field() }}
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Add new feature</strong><a href="{{ url()->previous() }}" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
				<div class="panel-body">
					<div class="form-group clearfix">
						<div class="col-xs-6">
							<label for="name">Feature Name</label>
							<input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
						</div>
						<div class="col-xs-6">
							<label for="category">Category</label>
							<select name="category" id="category" class="form-control">
								<option value="">None</option>
							@foreach($Categories as $Category)
								<option value="{{ $Category }}"{{ (old('category') == $Category)?' selected':'' }}>{{ $Category }}</option>
							@endforeach
								<option value="-1">New Category</option>
							</select>
							<div class="new_category_div" style="display: none;">
								<input type="text" class="form-control dynm" id="new_category" name="new_category" placeholder="New Category Name">
								<a href="javascript:NoNewCategory()" title="Remove"><span class="glyphicon glyphicon-remove pull-right dynmrem"></span></a>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label for="type">Value Type</label>
							<select class="form-control" name="value_type" id="type">
								<option value="YES/NO"{{ (old('value_type')=='YES/NO')?' selected':'' }}>YES/NO</option>
								<option value="STRING"{{ (old('value_type')=='STRING')?' selected':'' }}>STRING</option>
								<option value="OPTION"{{ (old('value_type')=='OPTION')?' selected':'' }}>OPTION</option>
								<option value="MULTISELECT"{{ (old('value_type')=='MULTISELECT')?' selected':'' }}>MULTISELECT</option>
							</select>
						</div>
					</div>
					<div class="form-group options clearfix col-xs-12">
						<label for="options">Options</label>
						<a href="javascript:AddNewOption()" title="Create New Option"><span class="glyphicon glyphicon-plus pull-right"></span></a>
						@if(null !== old('options') && !empty(old('options')))
							@foreach(old('options') as $iter => $option)
								<div class="option-pack">
									<input type="text" placeholder="Options" name="option[{{ $iter }}]" value="{{ $option }}" class="form-control dynm">
									<a href="javascript:DeleteOption({{ $iter }})" title="Delete this Option"><span class="glyphicon glyphicon-remove pull-right dynmrem"></span></a>
									<div class="clear"></div>
								</div>
							@endforeach
						@else
							<div class="option-pack">
								<input type="text" placeholder="Options" name="option[0]" value="" class="form-control dynm">
								<a href="javascript:DeleteOption(0)" title="Delete this Option"><span class="glyphicon glyphicon-remove pull-right dynmrem"></span></a>
								<div class="clear"></div>
							</div>
						@endif
					</div>
					<div class="form-group clearfix">
						<div class="col-xs-12">
							<label for="description">Description</label>
							<textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-xs-12">
							<label for="description_internal">Description (for internal purpose)</label>
							<div class="checkbox" style="margin-top: 0px;">
								<label for="same_as_public"><input id="same_as_public" type="checkbox"> Same as Public Description</label>
							</div>
							<textarea name="description_internal" id="description_internal" class="form-control">{{ old('description_internal') }}</textarea>
						</div>
					</div>
				
				</div>
				<div class="panel-footer clearfix">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</form></div>
		<div class="col-md-2"></div>
	</div>
</div>


@endsection
@push("css")
<style type="text/css">
.form-control.dynm { width:calc(100% - 20px) !important; float:left !important; }
.glyphicon.dynmrem { top:9px; }
.clear { clear:both; }
</style>
@endpush