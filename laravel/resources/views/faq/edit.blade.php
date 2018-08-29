@extends("faq.page")
@include('BladeFunctions')
@php
$Data = App\Models\FAQ::find(request()->id);
@endphp
@section("content")
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form method="post" class="form-horizontal">{{ csrf_field() }}
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Update FAQ</strong>{!! PanelHeadBackButton(Route('faq.index')) !!}</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Question</label>
                            <div class="col-md-10"><textarea class="form-control" name="question" rows="2">{{ $Data->question }}</textarea></div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Answer</label>
                            <div class="col-md-10"><textarea class="form-control" name="answer" rows="6">{{ $Data->answer }}</textarea></div>
                        </div>
                    </div>
                    <div class="panel-footer clearfix">
                        <input type="submit" name="submit" value="Update FAQ" class="btn btn-info pull-right">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection