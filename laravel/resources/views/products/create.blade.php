@extends("products.page")
@section("content")

<div class="content">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8"><form method="post" action="products" onSubmit="return ValidateBaseName()">{{ csrf_field() }}
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Add new product</strong><a href="products" class="btn btn-default pull-right btn-sm"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></div>
				<div class="panel-body">
					<div class="product_create_form">
						<div class="form-group clearfix">
							<div class="col-xs-6">
								<label for="name">Product Code</label>
								<input type="text" class="form-control" id="name" name="code" required value="{{ old('code')?:((new \App\Models\Product)->NextCode()) }}">
							</div>
							<div class="col-xs-6">
								<label for="basename">Base Name</label>
								<input type="text" class="form-control" id="basename" name="basename" required value="{{ old('basename') }}">
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12">
								<label for="name">Display Name</label>
								<input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
							</div>
						</div>
						<div class="form-group clearfix checkbox">
							<div class="col-xs-12">
								<label for="private"><input type="checkbox" name="private" value="YES" id="private"> Private Product (Doesn't publish in Websites)</label>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12">
								<label for="description">Description</label>
								<textarea name="description_public" id="description" class="form-control">{{ old('description_public') }}</textarea>
							</div>
						</div>
						<div class="form-group clearfix">
							<div class="col-xs-12">
								<label for="description_internal">Description (for internal purpose)</label>
								<div class="checkbox" style="margin-top: 0px">
									<label for="same_as_public"><input id="same_as_public" type="checkbox"> Same as Public Description</label>
								</div>
								<textarea name="description_internal" id="description_internal" class="form-control">{{ old('description_internal') }}</textarea>
							</div>
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
@push("js")
<script type="text/javascript">
$(function(){
	$("#same_as_public").on("change",function(){
		if($(this).prop("checked")){
			$("#description_internal").val($("#description").val()).attr("readonly","readonly");
		} else {
			$("#description_internal").removeAttr("readonly");
		}
	});
	$("#description").on("change",function(){
		$("#same_as_public").prop("checked",false);
		$("#description_internal").removeAttr("readonly")
	})
})
</script>
@endpush