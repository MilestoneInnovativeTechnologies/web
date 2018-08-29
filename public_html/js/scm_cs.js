// JavaScript Document


function ViewCustomer(Code){
	if(Code == '') return;
	FireAPI('api/v1/scm/customer/'+Code+'/details',ShowViewModal);
	modal = getViewModal();
	SetupViewModal(modal)
}

function ChangePresale(Code){
	
}

function LoginReset(Code,Name,Email){
	
}

function getViewModal(){
	ID = 'modalView'; modal = $('#'+ID);
	return (modal.length)?modal : GetBSModal('Details of Customer').attr({'id',ID}).appendTo('body');
}

function ShowViewModal(R){
	DistributeViewModalData(R);
	getViewModal().modal('show');
}

function DistributeViewModalData(R){
	
}

function SetupViewModal(M){
	tbd = M.find('.panel-body').html(GetBSTable('striped view_customer')).find('tbody');
	
}
