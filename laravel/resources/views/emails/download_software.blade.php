@extends('emails.layout')
@include('emails.functions')
@section("content")
		<strong>Hi, {{ $To }}</strong>
		<p>Thanks for showing interest in {{ $Product }}</p>@if(!empty($Packages))
		<p>{{ $Product }} can be downloaded in the following variants</p>
		<ul style="padding-left: 30px;">@php $TH = ''; @endphp
			@foreach($Packages as $PKGCode => $PKGDetails)
			<li><p><strong>{{ $PKGDetails[0] }}</strong><br>{{ $PKGDetails[1] }}</p></li>
			@php $TH .= '<th style="font-size: 13px;">'.$PKGDetails[0].'</th>'; @endphp
			@endforeach
		</ul>
		<p>Download suitable edition of {{ $Product }} from the below links</p>
		<table width="100%" border="0" cellpadding="3" cellspacing="3">
			<thead><tr><th align="left" style="font-size: 13px;">Editions</th>{!! $TH !!}</tr></thead>
			<tbody>@foreach($Editions as $EdtnCode => $Edition)
				<tr>
					<th align="left" style="font-size: 13px;">{{ $Edition[0] }}</th>
					@foreach($Packages as $PKGCode => $PKGDetails)
					<td align="center">
						@if(isset($DownloadKeys[$EdtnCode][$PKGCode]) && !empty($DownloadKeys[$EdtnCode][$PKGCode]))
						{!! Button('Download',Route('software.'.strtolower($PKGDetails[2]).'.download',['key'=>$DownloadKeys[$EdtnCode][$PKGCode]])) !!}
						@else
						-
						@endif
					</td>
					@endforeach
				</tr>
			@endforeach</tbody>
		</table>
		@else
		<p style="font-weight: 600; color:#660102">Sorry, No any download packages available for {{ $Product }}</p>
		@endif
		<p>Scroll down to know more about {{ $Product }} product and its editions</p>
		<strong>{{ $Product }}</strong>
		<p>{{ $Description }}</p>
		<strong>Editions of {{ $Product }}</strong>@if(!empty($Editions))
		<ul style="padding-left: 30px;">@foreach($Editions as $Edition)
			<li><p><strong>{{ $Edition[0] }}</strong><br>{{ $Edition[1] }}</p></li>
		@endforeach</ul>
		<p>Need even more details?, visit <a href="{{ Route('home') }}">Milestone Website</a></p>
		@else
		<p style="font-weight: 600; color:#660102">Sorry, No any editions available for {{ $Product }}</p>
		@endif
@endsection