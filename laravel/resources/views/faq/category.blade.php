@extends("faq.page")
@include('BladeFunctions')
@section("content")
@php $Data = \App\Models\FAQ::find(request()->id); $FCats = $Data->Categories ? $Data->Categories->categories : []; @endphp

<div class="content">
	<form method="post">
		{{ csrf_field() }}
		<div class="panel panel-default">
		<div class="panel-heading"><strong>Update Categories</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('faq.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="table-responsive"><table class="table table-striped"><tbody>
					<tr><th colspan="2">{!! nl2br($Data->question) !!}</th></tr>
					<tr><td colspan="2">{!! nl2br($Data->answer) !!}</td></tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr><th>Categories</th><th width="25%"><div class="input-group">
							<input type="text" class="form-control add_category" placeholder="New Category">
							<span class="input-group-addon"><i class="glyphicon glyphicon-plus" style="cursor: pointer;" onclick="AddCategory()"></i></span>
							</div></th>
					</tr>
					<tr><th colspan="2" class="categories">
							@forelse(\App\Models\FAQAllCategory::all() as $category)
								<div class="col-xs-2" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="category[]" value="{{ $category->name }}" @if($FCats && in_array($category->name,$FCats)) checked @endif> {{  $category->name }}</label></div>
								@empty
							@endforelse
						</th></tr>
					</tbody></table></div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Update Categories" class="btn pull-right btn-primary">
		</div>
	</div>
	</form>
</div>

@endsection
@push('js')
	<script>
        function AddCategory(){
            Category = $('.add_category').val(); if(Category == "") return;
            $.post('/api/v1/faq/add/fct',{c:Category},function(jp){
                if(jp && jp.name) NCC(jp.name);
                $('.add_category').val('')
            })
        }
        function NCC(n) {
            $('<div>').addClass('col-xs-2').css('padding', '0px').html([
                $('<label>').addClass('checkbox-inline').html([
                    $('<input>').attr({type: 'checkbox', name: 'category[]', value: n, checked: 'checked'}),
                    " " + n
                ])
            ]).appendTo(".categories");
        }
	</script>
@endpush