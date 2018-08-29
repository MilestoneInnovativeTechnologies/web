<div class="panel-default panel Q{{ $Faq->id }}">
    <div class="panel-heading" onclick="FAQShow('Q{{ $Faq->id }}')"><span style="margin-right: 20px; font-weight: bold">{{ $No }}.</span> {{ nl2br($Faq->question) }}
        <span class="tags pull-right"><i class="tags">{!! implode('</i><i class="tags">',$Faq->tags) !!}</i></span>
    </div>
    <div class="panel-body"><p>{!! nl2br($Faq->answer) !!}</p>
        <div class="helped">Was this help full to you <button class="btn btn-default btn-sm" onclick="FAQBenefits('Q{{ $Faq->id }}')"><i class="glyphicon glyphicon-thumbs-up"></i> Yes</button></div>
    </div>
</div>