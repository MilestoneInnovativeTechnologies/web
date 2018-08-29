@extends("faq.page")
@include('BladeFunctions')
@php
    //$Faqs = \App\Models\FAQ::my()->get();
    //dd($Faqs->toArray());
@endphp
@section("content")
    <div class="content">
        <div class="clearfix" style="margin-bottom: 10px; border-bottom: 2px solid #CCC">
            <span class="h2">FAQs</span>
            {!! PanelHeadButton(Route('faq'),'View all Public FAQs','share-alt','default') !!}
        </div>
        @php $No = 0; @endphp
        @php $MyFaqs = \App\Models\FAQ::my()->get(); @endphp
        @if($MyFaqs->isNotEmpty())
            <div style="margin-top: 30px; font-weight: bold">FAQs specialized for Me</div>
            @foreach($MyFaqs as $Faq)
                @php $No++ @endphp
                @component('faq.comp_faq_view',compact('Faq','No')) @endcomponent
            @endforeach
        @endif
        @php $MyFaqs = \App\Models\FAQ::myRole()->get(); @endphp
        @if($MyFaqs->isNotEmpty())
            <div style="margin-top: 30px; font-weight: bold">FAQs related to my Role</div>
            @foreach($MyFaqs as $Faq)
                @php $No++ @endphp
                @component('faq.comp_faq_view',compact('Faq','No')) @endcomponent
            @endforeach
        @endif
        @php $MyFaqs = \App\Models\FAQ::myProduct()->get(); @endphp
        @if($MyFaqs->isNotEmpty())
            <div style="margin-top: 30px; font-weight: bold">FAQs related to my Product</div>
            @foreach($MyFaqs as $Faq)
                @php $No++ @endphp
                @component('faq.comp_faq_view',compact('Faq','No')) @endcomponent
            @endforeach
        @endif

    </div>
@endsection
@push('css')
<style type="text/css">
    .panel {
        margin-bottom: 3px !important;
        border: none !important;
    }
    .panel-heading { cursor: pointer; }
    .panel .panel-body {
        display: none;
    }
    .panel.view .panel-body {
        display: block;
    }
    i.tags {
        font-size: 10px;
        padding: 3px 6px;
        background-color: #FFF;
        margin: 0px 2px;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
    }
    .helped {
        margin-top: 100px;
        font-size: 12px;
    }
</style>
@endpush
@push('js')
<script>
    function FAQShow(cls){
        if($('.panel.view.'+cls).length) return $('.panel.view.'+cls+' .panel-body').slideUp(100,function () {$(this).parent().removeClass('view')});
        $('.panel.view').removeClass('view').find(".panel-body").removeAttr('style');
        $('.panel.'+cls+' .panel-body').slideDown(200,function () {$(this).parent().addClass('view')});
        $.post('/api/ifv',{q:cls});
    }
    function FAQBenefits(cls){
        $.post('/api/ifb',{q:cls},function(cls){ $('.panel.'+cls+' .helped').text('Thanks for your feedback...').delay(2000).fadeOut() });
    }
</script>
@endpush