@extends("vacancy.page")
@include('BladeFunctions')
@section("content")
    <div class="content">
        <form method="post">{{ csrf_field() }}
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>New Vacancy</strong>{!! PanelHeadBackButton(Route('vacancy.manage')) !!}</div>
                    <div class="panel-body">
                        {!! formGroup(2,'code','text','Code',old('code',(new \App\Models\Vacancy)->NewCode()),['labelStyle' => 'text-align:left']) !!}
                        {!! formGroup(2,'title','text','Title',old('title',''),['labelStyle' => 'text-align:left']) !!}
                        {!! formGroup(2,'description','textarea','Description',old('description',''),['labelStyle' => 'text-align:left']) !!}
                        {!! formGroup(2,'date','text','Date',old('date',date('Y-m-d')),['labelStyle' => 'text-align:left']) !!}
                    </div>
                    <div class="panel-body specs" style="border-top: 1px solid #EEEEEE">
                        <h4 class="panel-heading">Specifications<a href="javascript:addSpecRow()" class="btn btn-default pull-right"><span class="glyphicon-plus glyphicon"></span></a></h4>
                        <div class="row template" style="display: none">
                            <div class="col-xs-4">
                                {!! formGroup(1,'spec-title','text','',old('description',''),['labelStyle' => 'text-align:left','attr' => 'placeholder="Specification Title"']) !!}
                            </div>
                            <div class="col-xs-7">
                                {!! formGroup(1,'spec-detail','textarea',false,old('detail',''),['labelStyle' => 'text-align:left','attr' => 'placeholder="Specification Detail"']) !!}
                            </div>
                            <div class="col-xs-1">
                                <div class="form-group clearfix">
                                    <label class="control-label " style="text-align:left"></label>
                                    <a href="javascript:delSpecRow()" class="btn btn-default"><span class="glyphicon-minus glyphicon"></span></a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="panel-footer clearfix">
                        <div class="pull-right">
                            <input type="submit" value="Post Vacancy" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('js')
    <script>
        $(function(){

        })
        function addSpecRow(id) {
            id = id || getSpecId();
            ChangeName($(".template").clone().appendTo($(".specs")).removeClass('template'),id)
        }
        function delSpecRow(id) {
            $("."+id).remove();
        }
        function getSpecId() {
            return ['spid',parseInt(Math.random()*10000)].join('');
        }
        function ChangeName(jE,N) {
            jE.find('[name="spec-title"]').attr('name','spec['+N+'][title]');
            jE.find('[name="spec-detail"]').attr('name','spec['+N+'][detail]');
            jE.find('a').attr('href','javascript:delSpecRow("'+N+'")');
            jE.removeAttr('style').addClass(N);
        }
    </script>
    <script type="text/javascript" src="js/datepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="css/datepicker.css">
    <script type="text/javascript">
        $(function(){
            $("[name='date']").datepicker({format:'yyyy-mm-dd',autoclose:true,defaultViewDate:'today'});
        })
    </script>
@endpush