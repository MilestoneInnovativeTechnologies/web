@extends("faq.page")
@include('BladeFunctions')
@php
$Prods = \App\Models\Product::where('active',1)->with(['Editions' => function($Q){ $Q->where('active',1); }])->get();
@endphp
@section("content")
    <div class="content">
        <form method="post" class="form-horizontal">{{ csrf_field() }}
            <div class="panel panel-default">
                <div class="panel-heading">Create New FAQ</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Question</label>
                                <div class="col-md-10"><textarea class="form-control" name="question" rows="2"></textarea></div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Answer</label>
                                <div class="col-md-10"><textarea class="form-control" name="answer" rows="6"></textarea></div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Category</label>
                                <div class="col-md-6">&nbsp;</div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control add_category" placeholder="New Category">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-plus" style="cursor: pointer;" onclick="AddCategory()"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-10 col-md-offset-2 categories">
                                    @forelse(\App\Models\FAQAllCategory::all() as $FC)
                                    <div class="col-xs-4" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="category[]" value="{{ $FC->name }}"> {{ $FC->name }}</label></div>
                                        @empty
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5" style="border-left: 1px solid #DDD; padding-left: 30px">
                            <div class="form-group">
                                <label>Scope</label>
                                <div>
                                    <div class="col-xs-6" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="public" value="public"> Public</label></div>
                                    <div class="col-xs-6" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="support" value="support"> All Support Team</label></div>
                                    <div class="col-xs-6" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="distributor" value="distributor"> All Distributor</label></div>
                                    <div class="col-xs-6" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="customer" value="customer"> All Customers</label></div>
                                    <div class="col-xs-6" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="partner" value="partner"><input name="partner_name" onkeyup="name_changed()" type="text" class="form-control" placeholder="Partner"></label></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Software</label>
                                <div>
                                    <div class="table-responsive"><table class="table table-condensed"><thead><tr><th>Product</th><th>Editions</th></tr></thead><tbody>
                                            @foreach($Prods as $Prod)
                                                <tr>
                                                    <td><label class="checkbox-inline"><input type="checkbox" name="product[]" value="{{ $Prod->code }}"> {{ $Prod->name }}</label></td>
                                                    <td>
                                                        <div class="col-xs-12" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="edition[{{ $Prod->code }}][All]" value="All" onchange="AllEditions('{{ $Prod->code }}')"> All Editions</label></div>
                                                        @foreach($Prod->Editions as $Edition)
                                                            <div class="col-xs-6" style="padding: 0px;"><label class="checkbox-inline"><input type="checkbox" name="edition[{{ $Prod->code }}][]" value="{{ $Edition->code }}"> {{  $Edition->name }}</label></div>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody></table></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="panel-footer clearfix">
                    <input type="submit" name="submit" value="Create FAQ" class="btn btn-info pull-right">
                </div>
            </div>
        </form>
    </div>
@endsection
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
        function AddCategory(){
            Category = $('.add_category').val(); if(Category == "") return;
            $.post('/api/v1/faq/add/fct',{c:Category},function(jp){
                if(jp && jp.name) NCC(jp.name);
                $('.add_category').val('')
            })
        }
        function NCC(n){
            $('<div>').addClass('col-xs-4').css('padding','0px').html([
                $('<label>').addClass('checkbox-inline').html([
                    $('<input>').attr({ type:'checkbox', name:'category[]', value:n, checked:'checked' }),
                    " "+n
                ])
            ]).appendTo(".categories");
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