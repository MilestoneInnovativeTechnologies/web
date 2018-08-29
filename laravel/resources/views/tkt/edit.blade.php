@extends("tkt.page")
@include('BladeFunctions')
@section("content")
@php $tkt = \App\Models\Ticket::find(Request()->tkt)->load('Attachments');/* dd($tkt->toArray());*/ @endphp

<div class="content"><form method="post" enctype="multipart/form-data">{{ csrf_field() }}<input name="customer" value="{{ $tkt->customer }}" type="hidden">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Edit Support Tickets</strong>{!! PanelHeadBackButton((url()->current() == url()->previous())?Route('tkt.index'):url()->previous()) !!}</div>
		<div class="panel-body">
			<div class="row">
				<div class="col col-md-6 left_section">
					{!! formGroup(2,'product','select','Product',$tkt->seqno,['labelWidth'	=>	4]) !!}
					{!! formGroup(2,'category','select','Category',$tkt->category,['labelWidth'	=>	4, 'selectOptions' => ['' => 'Other']]) !!}
				</div>
				<div class="col col-md-6" style="border-left:1px solid #CECECE">
					{!! formGroup(1,'title','text','Issue Title',$tkt->title,['labelWidth'	=>	4]) !!}
					{!! formGroup(1,'description','textarea','Issue Description',$tkt->description,['labelWidth'	=>	4, 'style'	=>	'height:160px']) !!}
					<table class="table table-striped attachments"><thead><tr><th colspan="2">Attachments</th><th><a href="javascript:AddAttachment()" class="btn btn-default btn-xs pull-right"><span class="glyphicon glyphicon-plus"></span></a></th></tr></thead><tbody>
					</tbody></table>
				</div>
			</div>
		</div>
		<div class="panel-footer clearfix">
			<input type="submit" name="submit" value="Update Ticket" class="btn btn-primary pull-right">
		</div>
	</div></form>
</div>

@endsection
@push('js')
<script type="text/javascript">
	var __CUSTOMER = '{{ $tkt->customer }}';
	@if($tkt->category && $tkt->Category->available == "onDemand")$(function(){
		Category = $('[name="category"]');
		Category.prepend($('<option>').attr({value:'{{ $tkt->category }}',selected:true}).text('{{ $tkt->Category->name }}'));
	})@endif @if($tkt->category && $tkt->Category_specs && $tkt->Category_specs->isNotEmpty()) var __CSPEC = {@foreach($tkt->Category_specs as $cspec)
	'{{ $cspec->Specification->code }}':'{{ ($cspec->value_text)?:$cspec->Value->code }}'@if($loop->remaining),@endif @endforeach} @endif
</script>
<script type="text/javascript" src="js/ticket_create.js"></script>@unless($tkt->Attachments->isEmpty())
<script type="text/javascript">
	$(function(){ @foreach($tkt->Attachments as $attachs) CreateAndAddAttachmentData('{{ $attachs->name }}','{{ $attachs->file }}','{{ $attachs->id }}'); @endforeach })
</script>
@endunless
@endpush