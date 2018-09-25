@extends("pra.articles.layout")
@section('content')
<style type="text/css">
	pre { padding: 0px 0px 0px 15px; border: none; background-color: transparent; display: inline-block; }
	blockquote footer { height: 30px; }
	blockquote footer a { display: inline-block; width: 150px; }
</style>
@php
$data = [
	'ForuM General Trading L.L.C' => [
		'Balance Sheet' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1XDqlG4CQPWNMh3Tin_dpRCzQiB4WYVbb'],
		],
		'Stock Report' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1kJ9dUJjshM57Dpfi3BZucWbFAzTZ5F_M'],
		],
		'Profit & Loss' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1MF9hFjco05LgGB4bR97J9Glu6vnJbZp6'],
		],
		'Trial Balance' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=11RLbaTIAiOhf2P_l8ZzQEwHkMr9igH1Z'],
		],
	],
	'Brisco General Trading L.L.C' => [
		'Balance Sheet' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1UawGiN1k1sz-tRLunUmKiiqCpqmL1R9H'],
		],
		'Stock Report' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1O8IH6qWHub1_BYpNV7iJXUDZSMzAWigB'],
		],
		'Profit & Loss' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1wWG7R_-u29YBZuGaIfOccHJeDKkuskpB'],
		],
		'Trial Balance' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1AoRVNCZBoAXbFiAmQfSjz9FciTJG9gXh'],
		],
	],
	'ForuM General Trading L.L.C (HO)' => [
		'Balance Sheet' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1Z5tLyy-HXRY9mjbqfF_6mKRP6D-qMdB4'],
		],
		'Stock Report' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1RQVyolX-ImavIY6RgyX7vUSr-XulzaGy'],
		],
		'Profit & Loss' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1uKTh_Z-KDcfzbvBxb-Bc6hV8OOSOvJSD'],
		],
		'Trial Balance' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1CwcRyG8LTguuskeHX-JUUl8k8ZGE_Wgk'],
		],
	],
	'Southland Auto Spare Parts Trading L.L.C' => [
		'Balance Sheet' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1to4mRcHDqPuT1Wplgi2WlxSmRpzXJ2nf'],
		],
		'Stock Report' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1ZWxZySch6E7pFRV_XX_9DTxcgGbQMDve'],
		],
		'Profit & Loss' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1xnrV1_70xROfxXuiauoszI6WHXX-kK_q'],
		],
		'Trial Balance' => [
			['Rev' => '0.1', 'Date' => '2018-09-13 10:50:00', 'Link' => 'https://drive.google.com/open?id=1iq_mYiAiXRmLwmVqWz0vQ0uLnCRTNaae'],
		],
	]
];
@endphp
<h2 style="margin-bottom: 40px;"><u class="">ForuM General Trading L.L.C - MIS Report - 01/01/2017 to 31/12/2017</u></h2>
@foreach($data as $Company => $Reports)
	<blockquote>
		<p>{{ $Company }}</p>
		@foreach($Reports as $Report => $Details)
					@foreach($Details as $detail)
						<footer><samp>Rev{{ $detail['Rev'] }}<pre>	</pre><a href="{{ $detail['Link'] }}" download target="_blank">{{ $Report }}</a><pre>	</pre>updated on {{ $detail['Date'] }} @if((time() - strtotime($detail['Date'])) < (1.5 * 24 * 60 * 60)) <kbd>new</kbd>	@endif</samp></footer>
					@endforeach
		@endforeach
	</blockquote>
@endforeach

@endsection