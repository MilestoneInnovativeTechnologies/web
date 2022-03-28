@extends("pa.articles.layout")
@section('content')

<div class="page_contents">
	<div class="page pa" style="background-color: #EBEBEB">
		<div class="container">


			<div style="margin-top: 120px; text-align: center; font-weight: bold; text-decoration: underline; font-size: 18px; color: #000000">How to add new Fiscal Year</div>
			<div style="margin-top: 6px; text-align: center; font-weight: bold; text-decoration: underline; font-size: 16px; color: #d10f0f">01-APRIL-2022 TO 31-MARCH-2023</div>
			<div style="margin-top: 6px; color: #000000">Step 1:  Open the ePlus software and from <strong>Tools</strong> menu select the option <strong>fiscal year</strong>.</div>
			<div style="margin-top: 6px; margin-left: 6%; font-size: 17px; color: #059505"><span style="font-size: 14px; color:#000000;">Go to </span>Tools<span style="font-size: 14px; color:#000000;"> > </span>Settings<span style="font-size: 14px; color:#000000;"> > </span>Fiscal year</div>
			<img src="https://i.ibb.co/9WThqw8/Step01.png" />
			<div style="margin: 6px 0px; color: #000000">Step 2: Select <span style="font-size: 17px; color: #059505">New</span> option</div>
			<img src="https://i.ibb.co/hHr15VY/Step02.png" />
			<div style="margin: 6px 0px; color: #000000">Step 3: <span style="font-size: 17px; color: #059505">Enter the new fiscal year details as shown below.</span> And then click on the <span style="font-size: 17px; color: #059505">Save</span> button.  </div>
			<img src="https://i.ibb.co/zRDKvJ9/Step03.png" />
			<div style="margin: 6px 0px; color: #000000">Step 4: After the “Data saved successfully” message appears, then <span style="font-size: 17px; color: #059505">close</span> the ePlus Application and <span style="font-size: 17px; color: #059505">Reopen ePlus Software and log in again</span>.</div>
			<div style="margin: 6px 0px; color: #000000">Now the new financial year will appear in the browse window.  All the transaction windows will ask you to select the fiscal year, and then to continue your recording journey by selecting the required year.</div>
			<img src="https://i.ibb.co/WgxXPJ0/Step04.png" />
			<div style="margin: 100px 0px; text-align: center">
				<h4 style="font-weight: bold; margin-bottom: 0px; text-decoration: underline">Watch a Video Demonstration on Adding Fiscal Year</h4><br />
				<iframe width="560" height="315" src="https://www.youtube.com/embed/W6pS4Imc4sc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>


		</div>
	</div>@include("home.section_contact_wo_map")</div>
		@include("home.login_modal")

@endsection
@push('js')
<script type="text/javascript">
function login(){
	$('#loginModal').modal('show');
}
</script>

@endpush