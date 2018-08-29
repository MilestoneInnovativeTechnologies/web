// JavaScript Document

$(function(){
	RearrangeProducts();
	CloseButtonsToPanels();
	CreateVersionDetailsTable();
	HideCustomerList();
})

function RearrangeProducts(){
	$.each(_Products,function(PID,Obj){
		$.each(Obj[2],function(k,Obj){
			EDN = Object.keys(Obj)[0];
			if(!_Products[PID][3]) _Products[PID][3] = new Object();
			if(!_Products[PID][3][EDN]) _Products[PID][3][EDN] = Obj[EDN][0];
		})
	})
}

function CloseButtonsToPanels(){
	$('.panel.panel-default').each(function(i,Ele){
		if(i == 0) return;
		$(Ele).find('.panel-heading').append(CreateCloseButton(i));
		HidePanel(i);
	})
}

function CreateCloseButton(n){
	return $('<span class="pull-right">').html($('<a>').attr({'href':'javascript:HidePanel("'+n+'")','class':'btn btn-default btn-sm'}).text('X'));
}

function HidePanel(n){
	$('.panel.panel-default:eq('+n+')').parent().slideUp(200);
}

function ShowPanel(n){
	$('.panel.panel-default:eq('+n+')').parent().slideDown(200);
}

function ProductDetails(PID){
	PName = _Products[PID][0]; PDesc = _Products[PID][1];
	ShowPanel(1); HidePanel(2); PanelHeading(1,PName)
	$('.product_details .panel-body').filter(':first').text(PDesc).end().filter(':gt(0)').remove();
	$('<div>').addClass('panel-body clearfix').insertAfter($('.product_details .panel-body')).html(getEditionDetails(_Products[PID][3]))
	$('html, body').animate({ scrollTop: $('.product_details').offset().top }, 500);
}

function PanelHeading(n,h){
	$('.panel.panel-default:eq('+n+') .panel-heading strong').text(h);
}

function getEditionDetails(EAry){
	return $('<ul>').html($.map(EAry,function(D,m){
		return $('<li>').html($('<strong>').text(D[0])).append('<br>').append(D[1]);
	}))
}

function DownloadLink(PID, EID){
	$('#downloadModal').modal('show'); PKG = $('#downloadModal [name="package"]').empty(); PKG.attr({'data-product':PID,'data-edition':EID});
	ValidityChanged(0);
	$.getJSON('api/v1/get_packages',{PID:PID,EID:EID},function(JA){
		$('#downloadModal [name="package"]').html(CreateNodes($.map(JA,function(Q){ return Q.packages; }),'option','name',{value:'code','data-type':'type'}))
	})
}

function GenerateDownloadLink(){
	PKG = $('#downloadModal [name="package"]'); VAL = $('[name="validity"]'); 
	ValidityChanged(1);
	$.get('api/v1/get_download_link',{PID:PKG.attr('data-product'),EID:PKG.attr('data-edition'),PKG:PKG.val(),VAL:VAL.val(),TYP:PKG.find("option:selected").attr('data-type')},function(JL){
		$('[name="generatedlink"]').val(JL).next().attr('href',JL);
	})
}

function ValidityChanged(i){
	if(i) return $('[name="generatedlink"]').val('please wait, creating link..').parent().slideDown().prev().slideUp();
	return $('[name="generatedlink"]').val('').parent().slideUp().prev().slideDown();
}

function SendProductInformation(PID){
	$('#mailModal').modal('show').attr('data-product',PID);
	FillEditions(PID); FillCustomers();
}

function FillEditions(PID){
	Editions = _Products[PID][3]; EDN = $('[name="edition"]').html($('<option value="*">').text('All Editions'));
	$.each(Editions,function(E,EAry){
		$('<option>').text(EAry[0]).attr('value',E).appendTo(EDN);
	})
}

function EditionChaged(){
	EDN = $('[name="edition"]');
	FillPackakges($('#mailModal').attr('data-product'),EDN.val())
}

function FillPackakges(PID,EID){
	$.getJSON('api/v1/get_packages',{PID:PID,EID:EID},function(JA){
		$('#mailModal [name="package"]').html($('<option value="*">').text('All Packages')).append(CreateNodes($.map(JA,function(Q){ return Q.packages; }),'option','name',{value:'code','data-type':'type'}))
	})
}

var _Customers = null;
function FillCustomers(){
	if(_Customers) return FillCustomerData(_Customers)
	$.getJSON('api/v1/my_customers',function(JC){
		_Customers = JC;
		FillCustomerData(_Customers);
	})
}

function FillCustomerData(Data){
	CUS = $('[name="customer"]').html($('<option value="0">').text('Select Customer'))
	CUS.append($('<option value="-1">').text('Guest Email - Enter Manually'))
	$.each(Data,function(E,N){
		CUS.append($('<option value="'+E+'">').text(N+' - '+E))
	})
}

function CustomerSelected(CE){
	if(CE == '-1') return GuestEmailEnter();
}

function NoGuestEmail(){
	$("#emailSelectCustomer").val('0').slideDown().next().slideUp().find('input').val('');
}

function GuestEmailEnter(){
	$("#emailSelectCustomer").slideUp().next().slideDown().find('input').val('');
}

function SendIDMail(){
	CUS = get_sidl_customer(); if(!CUS) return sidlError();
	PID = $('#mailModal').attr('data-product');
	EID = $('#mailModal [name="edition"]').val();
	PKG = $('#mailModal [name="package"]').val();
	$('#mailModal').modal('hide');
	$.get('api/v1/dd/sidl',{CUS:CUS,PID:PID,EID:EID,PKG:PKG},function(sidlr){
		alert('Mail Sent Successfully.')
	})
}

function get_sidl_customer(){
	CS = $('[name="customer"]'); CSV = CS.val(); GS = $('[name="guest_customer_email"]');
	if(CSV == "0") return false;
	if(CSV == "-1") return ($.trim(GS.val()) == '') ? false : GS.val();
	return CSV;
}

function sidlError(){
	$('<div>').addClass('text-danger mailsidlerror text-center').text('Customer not choosen, Please correct it..').prependTo($('#mailModal .modal-body'));
	setTimeout(function(){ return $('.mailsidlerror').remove(); },3000)
}

function ViewUpdates(PID, EID){
	ShowPanel(2); HidePanel(1); PanelHeading(2,_Products[PID][0]+' latest update');
	GetCustomerList(PID, EID)
	$.getJSON('api/v1/vpd',{PID:PID,EID:EID},function(jpu){
		$('div.customer_list').attr({'data-product':jpu.product, 'data-edition':jpu.edition, 'data-package':jpu.package.code, 'data-version':jpu.version_numeric});
		jpu.edition = get_edition_name(jpu.product,jpu.edition);
		TBD = $('table.table-version_details tbody');
		$.each(jpu,function(k,v){
			$("tr."+k+" td.tb_body",TBD).text(v);
		})
		$.each(jpu.package,function(k,v){
			$("tr."+k+" td.tb_body",TBD).text(v);
		})
		$("tr.action td.tb_body",TBD).html($('<a>').attr({href:'javascript:SendUpdateInformation()','class':'btn-link'}).text('Send information to Customer'));
		//HideCustomerList();
	})
}

function CreateVersionDetailsTable(){
	TBD = $('table.table-version_details tbody').empty();
	Fields = {'edition':'Edition','name':'Package','version_numeric':'Version','build_date':'Build Date','approved_date':'Approved On','action':'Actions','change_log':'Change Log'}
	$.each(Fields,function(c,N){ TBD.append($('<tr>').addClass(c).html([$('<th>').addClass('tb_head').text(N),$('<td>').addClass('tb_body')])) });
}

function get_edition_name(P,E){
	return _Products[P][3][E][0];
}

function GetCustomerList(PID, EID){
	$.getJSON('api/v1/get_my_product_customers',{PID:PID, EID:EID},function(JPC){
		FillCustomerList(JPC);
	})
}

function FillCustomerList(CJ){
	TBD = $('table.table-customer_list tbody').empty();
	TR = $('<tr>'); CB = $('<input>').attr({type:'checkbox',value:''}); TD1 = $('<td>');  TD2 = $('<td>');
	TR.clone().html(TD1.clone().attr('colspan',2).html([$('<a>').attr({href:'javascript:InvertCustomerSelection()','class':'btn btn-link'}).text('Invert selections'),$('<a>').attr({href:'javascript:SendUpdatesToSelectedCustomers()','class':'btn btn-link'}).text('Send Mail to selected customers')])).appendTo(TBD);
	$.each(CJ,function(C,EN){
		TR.clone().html([TD1.clone().html(CB.clone().val(C)),TD2.clone().text(EN[1]+' - '+EN[0])]).appendTo(TBD);
	})
}

function InvertCustomerSelection(){
	$('table.table-customer_list input[type="checkbox"]').each(function(i,E){
		$(E).prop("checked",!$(E).prop("checked"));
	})
}

function HideCustomerList(){
	$('div.customer_list').css("display","none");
}

function SendUpdateInformation(){
	$('div.customer_list').slideDown(200);
	$('table.table-customer_list input[type="checkbox"]').prop("checked",false);
}

function SendUpdatesToSelectedCustomers(){
	Mails = GetCheckedMails(); DIV = $('div.customer_list');
	PID = DIV.attr('data-product'); EID = DIV.attr('data-edition'); PKG = DIV.attr('data-package'); VER = DIV.attr('data-version');
	$.get('api/v1/dd/sputc',{CUS:Mails,PID:PID,EID:EID,PKG:PKG,VER:VER},function(R){
		console.log(R)
	})
}

function GetCheckedMails(){
	Mails = [];
	$('table.table-customer_list input[type="checkbox"]:checked').each(function(j,Cb){
		Mails.push(Cb.value);
	})
	return Mails;
}