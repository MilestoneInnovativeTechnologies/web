<?php



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

// If the requesting role have the resource, mentioned in wapi are allowed..

Route::get('/', 'HomeController@index')->name('home');
Route::get('/print_objects', function(){ return view('home.print_objects'); });
Route::get('/vacancy', function(){ return view('home.vacancies'); });
Route::get('/print_object/{code}/download', "PublicPrintObjectController@download")->name('home.print_object.download');
Route::get('/faq', function(){ return view('home.faq'); })->name('faq');
//Route::get('/', function(){ return 'as'; });
Route::get('refresh', 'Refresh@index');
Route::post('refresh','Refresh@post');



Route::get('initlogin/{code}','InitLoginController@login')->name('initlogin');
Route::post('initlogin/{code}','InitLoginController@change');

Route::prefix("brand")->group(function(){
	Route::get('/', 'BrandController@index')->name('db.brand');
});

Route::prefix("test")->group(function(){
	Route::get('1', function(){
		//DB::enableQueryLog();
		//\App\Models\Notification::all();
		//return DB::getQueryLog();
  });
	Route::get('2', 'Test@index');
	Route::get('5', 'TicketController@ttt');
	Route::get('3', function(){
	    $Response = \App\Libraries\SMSGateways\SmppSMSHub::init('9495155550','Test Msg From MIT web using API. Time: '.date('Y-m-d H:i:s'))->send();
	    dd($Response);
    });
	Route::get('4', function(){
		return \DB::table('v_dailyticketstatus')->get()->toArray();
	});
});


Route::get('download/ticket/uploadedfile/{tkt}/{id}', 'CommonController@ticketUploadedFileDownload')->name("ticket.uploadedfile.download");
Route::get('software/update/download/{key}', 'CommonController@softwareUpdateDownload')->name("software.update.download");
Route::get('software/onetime/download/{key}', 'CommonController@softwareOnetimeDownload')->name("software.onetime.download");
Route::get('software/download/{key}', 'CommonController@softwareDownload')->name("software.download");
Route::get('download/printobject/{key}', 'CommonController@support_print_object_download')->name("support.printobject.download");
Route::get('printobject/download/{key}', 'CommonController@print_object_download')->name("printobject.download");
Route::get('forms/general/upload/{key}', 'CommonController@general_upload_form')->name("general.uploadform");
Route::post('forms/general/upload/{key}', 'CommonController@general_upload_form');
Route::get('download/general/uploaded/{key}', 'CommonController@generalform_uploaded_download')->name("download.generalform.uploaded");
Route::get('view/image/ticket/conversation/{path}', 'CommonController@browser_display')->name("ticket.conversation.image")->where('path', '(.*)');
Route::get('download/ticket/attachment/{path}', 'CommonController@file_download')->name("ticket.download.attachment")->where('path', '(.*)');
Route::get('download/backup/database/{key}', 'CommonController@database_backup_download')->name("backup.database.download");
Route::get('mwm/copyright/{key}', 'CommonController@webmail_track')->name("webmail.track");
Route::get('article/p/{code}', 'PublicArticleController@serve')->name('article.serve.public');
Route::get("notification/{code}/serve","NotificationController@serve")->name("notification.serve");
Route::get('article/{code}/serve', 'PrivateArticleController@serve')->name('article.serve.private');
Route::get('tools/{code}/download/{key}', 'ThirdPartyApplicationController@download')->name('tpa.download');


Route::get('sms/post/json',"SmsJsonController@Index");
Route::post('sms/post/json',"SmsJsonController@Store");

Route::get('backup/database/upload',"DatabaseBackupStoreController@Index");
Route::post('backup/database/upload',"DatabaseBackupStoreController@Store");

Route::get('backup/database/registration','DatabaseBackupRegistrationController@Index');
Route::post('backup/database/registration','DatabaseBackupRegistrationController@Validation');

Route::get('backup/package/create','DatabaseBackupPackageController@Index');
Route::post('backup/package/create','DatabaseBackupPackageController@Validation');



// Product
Route::group(["middleware"=>["rolecheck:products"]],function(){
	Route::prefix("products")->group(function(){
		Route::get('create', 'ProductController@create');
		Route::post('/', 'ProductController@store');
		Route::get('/', 'ProductController@index');
		Route::get('{code}', 'ProductController@edit');
	});
	Route::prefix("product")->group(function(){
		Route::get('{code}', 'ProductController@show');
		Route::post('{code}', 'ProductController@update');
		Route::get('delete/{code}', 'ProductController@destroy');
		Route::get('{code}/features', 'ProductController@features');
		Route::post('{code}/features', 'ProductController@updatefeatures');
		Route::get('{code}/editions', 'ProductController@editions');
		Route::post('{code}/editions', 'ProductController@updateeditions');
		Route::get('{pcode}/edition/{ecode}/features', 'ProductController@editionfeature');
		Route::post('{Product}/edition/{Edition}/features', 'ProductController@updateeditionfeature');
		Route::get('{Product}/packages', 'ProductController@packages');
		Route::post('{Product}/packages', 'ProductController@updatepackages');
	});
});

//Edition
Route::group(["middleware"=>["rolecheck:editions"]],function(){
	Route::resource('editions', 'EditionController');
});

//Packages
Route::group(["middleware"	=>	["rolecheck:packages"]],function(){
	Route::get('package/download/{Product}/{Edition}/{Package}/{Seqno}', 'PackageController@download');
	Route::get('package/upload/{Product?}/{Edition?}/{Package?}', 'PackageController@upload');
	Route::post('package/upload/{Product}/{Edition}/{Package}', 'PackageController@doupload');
	Route::get('package/latest', 'PackageController@latest');
	Route::get('pkg/delete', 'PackageController@delete');
	Route::post('pkg/delete', 'PackageController@dodelete');
	Route::get('verify', 'PackageController@verify');
	Route::post('verify', 'PackageController@doverify');
	Route::get('approve', 'PackageController@approve');
	Route::post('approve', 'PackageController@doapprove');
	Route::get('revert', 'PackageController@revert');
	Route::post('revert', 'PackageController@dorevert');
	Route::resource('packages', 'PackageController');
});


//Features
Route::group(["middleware"	=>	["rolecheck:features"]],function(){
	Route::resource('features', 'FeatureController');
});


//Customer Dashboard
Route::group(["middleware"	=>	["rolecheck:customerdashboard"]],function(){
	Route::prefix("customer")->group(function(){
		Route::get("{seqno}/register","CustomerPage@register")->name("register.product");
		Route::post("{seqno}/register","CustomerPage@doregister");
		Route::get("dashboard","CustomerPage@dashboard")->name("customer.dashboard");
		Route::get("password","CustomerPage@password")->name("customer.password");
		Route::post("password","CustomerPage@changepassword");
		Route::get("address","CustomerPage@address")->name("customer.address");
		Route::post("address","CustomerPage@changeaddress");
	});
});

//Customer
Route::group(["middleware"	=>	["rolecheck:customers"]],function(){
	Route::prefix("customer")->group(function(){
		Route::prefix("{customer}")->group(function(){
			Route::prefix("{seqno}")->group(function(){
				Route::get('product/change',"CustomerController@changeproduct")->name("customer.changeproduct");
				Route::post('product/change',"CustomerController@dochangeproduct");
				Route::get('register',"CustomerController@register")->name("customer.register");
				Route::post('register',"CustomerController@regrequest");
			});
			Route::get('distributor/change',"CustomerController@change_distributor")->name("customer.changedistributor");
			Route::post('distributor/change',"CustomerController@dochange_distributor");
			Route::get('ondemancategories',"CustomerController@ondemancategories")->name("customer.ondemancategories");
			Route::get("resetlogin","CustomerController@resetlogin")->name("customer.resetlogin");
			Route::get('presale',"CustomerController@presale")->name("customer.presale");
			Route::get('edit',"CustomerController@edit")->name("customer.edit");
			Route::post('edit',"CustomerController@update");
			Route::post('presale',"CustomerController@storepresale");
			Route::get('tickets',"CustomerController@tickets")->name("customer.tickets");
		});
		Route::get('new',"CustomerController@add")->name('customer.new');
		Route::post('new',"CustomerController@create");
		Route::get('list',"CustomerController@index")->name("customer.index");
		Route::get('error',function(){ return view("customer.error")->with(["item"=>"customer"]); })->name('customer.error');
		Route::get('{customer}',"CustomerController@show")->name("customer.show");
	});
	Route::get('panel/customer/{code}', function(){ return view('customer.detail1'); })->name('customer.panel');
	Route::get("registration/details","CustomerRegistrationController@regdetails")->name("reg.detail");
});



Auth::routes();

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::get('password/forget', 'HomeController@forget')->name('password.forget');
Route::post('password/forget', 'HomeController@send_reset_link');
Route::get('home', 'HomeController@index')->name('home');
//Route::get('loginsuccess', 'LoginSuccess@index');



//Role
Route::group(["middleware"	=>	["rolecheck:roles"]],function(){
	Route::prefix("role")->group(function(){
		Route::get('{role}/resource','RoleController@resource')->name('role.resource');
		Route::post('{role}/resource','RoleController@resourceupdate');
		Route::get('error',function(){ return view("role.error")->with(["item"=>"role"]); })->name('role.error');
	});
	Route::resource('role', 'RoleController');
});
Route::get("roleselect/{role?}","RoleController@select")->name("roleselect")->middleware("auth");
Route::get("denied","RoleController@denied")->name("roledenied")->middleware("auth");



//Partner
Route::group(["middleware"	=>	["rolecheck:partner"]],function(){
	Route::resource('partner', 'PartnerController');
});


//Action
Route::group(["middleware"	=>	["rolecheck:actions"]],function(){
	Route::get('action/error',function(){ return view("action.error")->with(["item"=>"action"]); })->name('action.error');
	Route::resource('action', 'ActionController');
});



//Resource
Route::group(["middleware"	=>	["rolecheck:resources"]],function(){
	Route::prefix("resource")->group(function(){
		Route::get('{resource}/role','ResourceController@role')->name('resource.role');
		Route::post('{resource}/role','ResourceController@roleupdate')->name('resource.role');
		Route::get('error',function(){ return view("resource.error")->with(["item"=>"resource"]); })->name('resource.error');
	});
	Route::resource('resource', 'ResourceController');
});


//Dashboard
Route::get("dashboard",function(){
	session()->reflash();
	$RouteName = \App\Models\Role::whereCode(session("_role"))->first()->name . ".dashboard";
	return redirect()->route($RouteName);
})->middleware("auth")->name("dashboard");
Route::get("changeaddress",function(){
	$RouteName = \App\Models\Role::whereCode(session("_role"))->first()->name . ".address";
	session()->reflash();
	return redirect()->route($RouteName);
})->middleware("auth")->name("changeaddress");
Route::get("changepassword",function(){
	$RouteName = \App\Models\Role::whereCode(session("_role"))->first()->name . ".password";
	session()->reflash();
	return redirect()->route($RouteName);
})->middleware("auth")->name("changepassword");



//Dealer Dashboard
Route::group(["middleware"	=>	["rolecheck:dealerdashboard"]],function(){
	Route::prefix("dealer")->group(function(){
		Route::get("dashboard","DealerController@dashboard")->name("dealer.dashboard");
		Route::get("password","DealerController@password")->name("dealer.password");
		Route::get("address","DealerController@address")->name("dealer.address");
		Route::post("password","DealerController@changepassword");
		Route::post("address","DealerController@changeaddress");
		Route::get("tickets","DealerController@tickets")->name("dealer.tickets");
	});
});


Route::group(["middleware"	=>	["rolecheck:dealers"]],function(){
	Route::prefix("dealer")->group(function(){
		Route::get("{dealer}/delete","DealerController@destroy");
		Route::get("{dealer}/products","DealerController@products")->name("dealer.products");
		Route::post("{dealer}/products","DealerController@updateproducts");
		Route::get("{dealer}/countries","DealerController@countries")->name("dealer.countries");
		Route::post("{dealer}/countries","DealerController@updatecountries");
		Route::get("error",function(){ return view("dealer.error")->with(["item"	=>	"dealer"]); })->name("dealer.error");
	});
	Route::get('panel/dealer/{code}', function(){ return view('dealer.detail1'); })->name('dealer.panel');
	Route::resource('dealer', 'DealerController');
});


//Distributor Dashboard
Route::group(["middleware"	=>	["rolecheck:distributordashboard"]],function(){
	Route::prefix("distributor")->group(function(){
		Route::get("dashboard","DistributorController@dashboard")->name("distributor.dashboard");
		Route::get("password","DistributorController@password")->name("distributor.password");
		Route::get("address","DistributorController@address")->name("distributor.address");
		Route::post("password","DistributorController@changepassword");
		Route::post("address","DistributorController@changeaddress");
		Route::get("tickets","DistributorController@tickets")->name("distributor.tickets");
	});
});


//Distributor
Route::group(["middleware"	=>	["rolecheck:distributors"]],function(){
	Route::prefix("distributor")->group(function(){
		Route::prefix('{distributor}')->group(function(){
			Route::get("delete","DistributorController@destroy");
			Route::get("products","DistributorController@products")->name("distributor.products");
			Route::post("products","DistributorController@updateproducts");
			Route::get("countries","DistributorController@countries")->name("distributor.countries");
			Route::post("countries","DistributorController@updatecountries");
			Route::get("customers","DistributorController@customers")->name("distributor.customers.list");
			Route::get("tickets","DistributorController@tickets")->name("distributor.created.tickets");
			Route::get("resetlogin","DistributorController@resetlogin")->name("distributor.resetlogin");
			Route::get("supportteam",function(){ return view('distributor.change_supportteam'); })->name("distributor.supportteam");
			Route::post("supportteam","DistributorController@supportteam")->name("distributor.supportteam");
			Route::get("delete","DistributorController@delete")->name("distributor.delete");
			Route::get("support_categories",function(){ return view('distributor.support_categories'); })->name("distributor.support_categories");
			Route::post("support_categories","DistributorController@support_categories");
			Route::get("contact_options",function(){ return view('distributor.contact_options'); })->name("distributor.contact_options");
			Route::post("contact_options","DistributorController@contact_options");
		});
		Route::get("error",function(){ return view("distributor.error")->with(["item"	=>	"distributor"]); })->name("distributor.error");
	});
	Route::get('panel/distributor/{code}', function(){ return view('distributor.detail1'); })->name('distributor.panel');
	Route::resource('distributor', 'DistributorController');
});


//Price List
Route::group(["middleware"	=>	["rolecheck:pricelist"]],function(){
	Route::get('pricelist/error',function(){ return view("pricelist.error")->with(["item"=>"pricelist"]); })->name("pricelist.error");
	Route::resource('pricelist', 'PriceListController');
});

//Company Dashboard
Route::group(["middleware"	=>	["rolecheck:company"]],function(){
	Route::prefix("mit")->group(function(){
		Route::prefix("transaction")->group(function(){
			Route::post("{distributor}/new","TransactionController@CompanyNewTransaction")->name("mit.newdist.transaction");
		});
		Route::prefix("product")->group(function(){
			Route::get("pum","ProductUpdateMailer@index")->name("product.update.mailer");
		});
		Route::prefix("list")->group(function(){
			Route::get("registration/details","CustomerRegistrationController@regdetails")->name("mit.reg.detail");
			Route::get("team/{team}/status/{status}","TicketController@listtickets")->name("mit.tickets.list");
			Route::get("agent/{agent}/status/{status}","TicketTaskController@listtasks")->name("mit.tasks.list");
			Route::get("transactions/{distributor}","CompanyController@DistributorTransaction")->name("mit.transaction.list");
		});
		Route::get("{customer}/{seqno}/licence","CompanyController@getlicence")->name("download.customer.licence");
		Route::get("{customer}/{seqno}/register","CompanyController@register")->name("customer.registration");
		Route::post("{customer}/{seqno}/register","CompanyController@doregister");
		Route::get("customer/{code}",function(){ return redirect()->route('customer.panel',Request()->code); })->name("mit.customer.panel");
		Route::get("dealer/{code}",function(){ return view('dealer.detail1'); })->name("mit.dealer.panel");
		Route::get("supportteam/{code}",function(){ return redirect()->route('supportteam.panel',Request()->code); })->name("mit.supportteam.panel");
		Route::get("supportagent/{code}",function(){ return view('tsa.detail1'); })->name("mit.supportagent.panel");
		Route::get("distributor/{code}/transactions","CompanyController@transaction")->name("distributor.transactions");
		Route::get("distributor/{code}",function(){ return redirect()->route('distributor.panel',Request()->code); })->name("mit.distributor.panel");
		Route::get("ticket/{code}",function(){ return redirect()->route('ticket.panel',Request()->code); })->name("mit.ticket.panel");
		Route::get("task/{code}",function(){ return redirect()->route('task.panel',Request()->code); })->name("mit.task.detail");
		Route::get("password","CompanyController@password")->name("company.password");
		Route::get("address","CompanyController@address")->name("company.address");
		Route::post("password","CompanyController@changepassword");
		Route::post("address","CompanyController@changeaddress");
		//Route::get("dashboard",function(){ return view('company.dashboard'); });
		Route::get("/","CompanyController@index")->name("company.dashboard");
	});
	Route::get("regreqs","CompanyController@regreqs")->name("registration.requests");
});




//Log
Route::group(["middleware"	=>	["rolecheck:log"]],function(){
	Route::prefix("log")->group(function(){
		Route::get('unusr','AppLogController@unknownuser')->name('log.unusr');
	});
});


//Support Department
Route::group(["middleware"	=>	["rolecheck:supportdepartment"]],function(){
	Route::get('sd/{code}/undelete', 'SupportDepartmentController@undelete')->name("sd.undelete");
	Route::get('sd/{code}/delete', 'SupportDepartmentController@delete')->name("sd.delete");
	Route::resource('sd', 'SupportDepartmentController');
});


//Support Ticket Types
Route::group(["middleware"	=>	["rolecheck:supporttickettypes"]],function(){
	Route::get('stt/{code}/undelete', 'TicketTypeController@undelete')->name("stt.undelete");
	Route::get('stt/{code}/delete', 'TicketTypeController@delete')->name("stt.delete");
	Route::resource('stt', 'TicketTypeController');
});

//Support Types
Route::group(["middleware"	=>	["rolecheck:supporttype"]],function(){
	Route::get('st/{code}/undelete', 'SupportTypeController@undelete')->name("st.undelete");
	Route::get('st/{code}/delete', 'SupportTypeController@delete')->name("st.delete");
	Route::resource('st', 'SupportTypeController');
});

//Ticket Detail Types
Route::group(["middleware"	=>	["rolecheck:ticketdetailtype"]],function(){
	Route::get('tdt/{code}/undelete', 'TicketDetailTypeController@undelete')->name("tdt.undelete");
	Route::get('tdt/{code}/delete', 'TicketDetailTypeController@delete')->name("tdt.delete");
	Route::resource('tdt', 'TicketDetailTypeController');
});

//Ticket Statuses
Route::group(["middleware"	=>	["rolecheck:supportticketstatus"]],function(){
	Route::get('ts/{code}/undelete', 'TicketStatusController@undelete')->name("ts.undelete");
	Route::get('ts/{code}/delete', 'TicketStatusController@delete')->name("ts.delete");
	Route::resource('ts', 'TicketStatusController');
});

//Support Team
Route::group(["middleware"	=>	["rolecheck:supportteam"]],function(){
	Route::prefix('tst')->group(function(){
		Route::get('{code}/undelete', 'SupportTeamController@undelete')->name("tst.undelete");
		Route::get('{code}/delete', 'SupportTeamController@delete')->name("tst.delete");
		Route::get('{code}/distributors', 'SupportTeamController@distributors')->name("tst.distributors");
		Route::get('{code}/distributor/assign', 'SupportTeamController@distributors_assign')->name("tst.distributors.assign");
		Route::get('{code}/customers', 'SupportTeamController@customers')->name("tst.customers");
		Route::get('{code}/customers/assign', 'SupportTeamController@customers_assign')->name("tst.customers.assign");
	});
	Route::resource('tst', 'SupportTeamController');
	Route::get('panel/supportteam/{code}', function(){ return view('tst.detail1'); })->name('supportteam.panel');
});


//Product Info
Route::group(["middleware"	=>	["rolecheck:productinfo"]],function(){
	Route::get('productinfo', 'ProductInformationController@index')->name("product.information");
});


//Webdeveloper
Route::get('webdeveloper/dashboard','RoleController@index')->name('webdeveloper.dashboard');


//SCMController
Route::group(["middleware"	=>	["rolecheck:scmcontroller"]],function(){
	Route::prefix("scm")->group(function(){
		Route::get('dashboard','PackageController@latest')->name('scm.dashboard');
//		Route::prefix("customer")->group(function(){
//			Route::get('search','SCMController@CustomerSearch')->name('scm.customer.search');
//		});
	});
});

//Support Team Page
Route::group(["middleware"	=>	["rolecheck:supportteampage"]],function(){
	Route::prefix("stp")->group(function(){
		Route::get('product/interact', 'SupportTeamPageController@product_interact')->name('stp.product.interact');
		Route::get('distributor/{code}', 'SupportTeamPageController@edit_distributor')->name('stp.distributor.edit');
		Route::post('distributor/{code}', 'SupportTeamPageController@update_distributor');
		Route::get('customer/new', 'SupportTeamPageController@new_customer')->name('stp.customer.new');
		Route::post('customer/new', 'SupportTeamPageController@add_customer');
		Route::get('customer/{code}', 'SupportTeamPageController@edit_customer')->name('stp.customer.edit');
		Route::post('customer/{code}', 'SupportTeamPageController@update_customer');
		Route::get('customers', 'SupportTeamPageController@list_customers')->name('stp.customers');
		Route::get('distributors', 'SupportTeamPageController@list_distributors')->name('stp.distributors');
		//Route::get('distributors', 'DistributorController@index')->name('stp.distributors');
		Route::get('dashboard', function(){ return view('tst.dashboard'); })->name('supportteam.dashboard');
	});
});


//Support Agent Dashboard
Route::group(["middleware"	=>	["rolecheck:supportagentdashboard"]],function(){
	Route::get('tsa/dashboard', function(){ return view('tsa.dashboard'); })->name('supportagent.dashboard');
});


//Technical Support Agent
Route::group(["middleware"	=>	["rolecheck:supportagent"]],function(){
	Route::prefix('tsa')->group(function(){
		Route::get('{tsa}/tktprv', 'TechnicalSupportAgentController@ticket_privilages')->name('tsa.tkt.prv');
		Route::post('{tsa}/tktprv', 'TechnicalSupportAgentController@update_ticket_privilages');
		Route::get('{tsa}/delete', 'TechnicalSupportAgentController@delete')->name('tsa.delete');
		Route::get('{tsa}/login_reset', function(){ return view('tsa.login_reset'); })->name('tsa.login_reset');
		Route::post('{tsa}/login_reset', 'TechnicalSupportAgentController@login_reset');
		Route::get('department', 'TechnicalSupportAgentController@list_departments')->name('tsa.department');
		Route::post('department', 'TechnicalSupportAgentController@update_departments');
	});
	Route::get('panel/supportagent/{code}', function(){ return view('tsa.detail1'); })->name('supportagent.panel');
	Route::resource('tsa', 'TechnicalSupportAgentController');
});



//Customer Cookies
Route::group(["middleware"	=>	["rolecheck:customercookie"]],function(){
	Route::prefix('tscc')->group(function(){});
	Route::resource('tscc', 'CustomerCookieController');
});

//Customer Remote Connection
Route::group(["middleware"	=>	["rolecheck:remoteconnection"]],function(){
	Route::prefix('crc')->group(function(){});
	Route::resource('crc', 'CustomerRemoteConnectionController');
});

//Ticket
Route::group(["middleware"	=>	["rolecheck:ticket"]],function(){
	Route::prefix('tkt')->group(function(){
		Route::get('dsd/{id}', 'TicketController@download_support_document')->name('download.support.doc');
		Route::prefix('{tkt}')->group(function(){
			Route::get('view', 'TicketController@view')->name('tkt.view');
			Route::get('entitle', 'TicketController@entitle')->name('tkt.entitle');
			Route::post('entitle', 'TicketController@entitled');
			Route::get('tasks', 'TicketController@tasks')->name('tkt.tasks');
			Route::get('edit', 'TicketController@edit')->name('tkt.edit');
			Route::post('edit', 'TicketController@update');
			Route::get('delete', 'TicketController@delete')->name('tkt.delete');
			Route::get('reopen', 'TicketController@reopen')->name('tkt.reopen');
			Route::post('reopen', 'TicketController@doreopen');
			Route::get('close', 'TicketController@close')->name('tkt.close');
			Route::post('close', 'TicketController@doclose');
			Route::get('reassign', 'TicketController@reassign')->name('tkt.reassign');
			Route::post('reassign', 'TicketController@doreassign');
			Route::get('feedback', 'TicketController@feedback')->name('tkt.feedback');
			Route::post('feedback', 'TicketController@submitfeedback');
			Route::get('complete', 'TicketController@complete')->name('tkt.complete');
			Route::post('complete', 'TicketController@docomplete');
			Route::get('closure', 'TicketController@closure')->name('tkt.closure');
			Route::post('closure', 'TicketController@doclosure');
			Route::get('recreate', 'TicketController@recreate')->name('tkt.recreate');
			Route::post('recreate', 'TicketController@dorecreate');
			Route::get('communicate', 'TicketController@communicate')->name('tkt.communicate');
			Route::get('enquire', 'TicketController@enquire')->name('tkt.enquire');
			Route::get('req_complete', 'TicketController@req_complete')->name('tkt.req_complete');
			Route::post('req_complete', 'TicketController@req_complete_mail');
			Route::get('force_complete', 'TicketController@force_complete')->name('tkt.force_complete');
			Route::post('force_complete', 'TicketController@docomplete');
			Route::get('transcript', 'TicketController@transcript')->name('tkt.transcript');
			Route::post('transcript', 'TicketController@mail_transcript');
			Route::get('dismiss', 'TicketController@dismiss')->name('tkt.dismiss');
			Route::post('dismiss', 'TicketController@dodismiss');
		});
		Route::get('closuredoc/{tkt}', 'TicketController@get_closuredoc')->name('tkt.closuredoc');
		Route::get('category/tickets', 'TicketCategoryController@category_list')->name('category.tickets');
		Route::get('category', function(){ return view('tkt.category_report'); })->name('category.report');
		Route::get('create', 'TicketController@create')->name('tkt.create');
		Route::post('create', 'TicketController@store');
		Route::get('new', 'TicketController@tickets_new')->name('tkt.new');
		Route::get('opened', 'TicketController@tickets_opened')->name('tkt.opened');
		Route::get('inprogress', 'TicketController@tickets_inprogress')->name('tkt.inprogress');
		Route::get('hold', 'TicketController@tickets_holded')->name('tkt.holded');
		Route::get('closed', 'TicketController@tickets_closed')->name('tkt.closed');
		Route::get('completed', 'TicketController@tickets_completed')->name('tkt.completed');
		Route::get('/', 'TicketController@index')->name('tkt.index');
	});
	Route::get('panel/ticket/{code}', function(){ return view('tkt.detail1'); })->name('ticket.panel');
	Route::get("list/team/{team}/status/{status}","TicketController@listtickets")->name("tickets.list");
});

//Ticket Tasks
Route::group(["middleware"	=>	["rolecheck:tickettask"]],function(){
	Route::prefix('tsk')->group(function(){
		Route::get('{tsk}/edit', 'TicketTaskController@edit')->name('tsk.edit');
		Route::get('{tsk}/chngrsp', 'TicketTaskController@chngrsp')->name('tsk.chngrsp');

		Route::get('{tsk}/delete', 'TicketTaskController@delete')->name('tsk.delete');
		Route::get('{tsk}/open', 'TicketTaskController@open')->name('tsk.open');
		Route::get('{tsk}/recheck', 'TicketTaskController@recheck')->name('tsk.recheck');
		Route::get('{tsk}/work', 'TicketTaskController@work')->name('tsk.work');
		Route::get('{tsk}/hold', 'TicketTaskController@hold')->name('tsk.hold');
		Route::get('{tsk}/close', 'TicketTaskController@close')->name('tsk.close');
		
		Route::post('{tsk}/hold', 'TicketTaskController@dohold');
		Route::post('{tsk}/recheck', 'TicketTaskController@dorecheck');
		Route::post('{tsk}/edit', 'TicketTaskController@update');
		Route::post('{tsk}/chngrsp', 'TicketTaskController@update_responder');
		
		Route::get('new', 'TicketTaskController@tasks_new')->name('tsk.new');
		Route::get('working', 'TicketTaskController@tasks_working')->name('tsk.working');
		Route::get('holded', 'TicketTaskController@tasks_holded')->name('tsk.holded');
		Route::get('closed', 'TicketTaskController@tasks_closed')->name('tsk.closed');
		Route::get('{tsk}', 'TicketTaskController@view')->name('tsk.view');
		Route::get('/', 'TicketTaskController@index')->name('tsk.index');
	});
	Route::get('panel/task/{code}', function(){ return view('tsk.detail1'); })->name('task.panel');
	Route::get("list/agent/{agent}/status/{status}","TicketTaskController@listtasks")->name("tasks.list");
});

//Maintenance Contract
Route::group(["middleware"	=>	["rolecheck:maintenancecontract"]],function(){
	Route::prefix('mc')->group(function(){
		Route::prefix('{mc}')->group(function(){
			Route::get('view', 'MaintenanceContractController@view')->name('mc.view');
			Route::get('modify', 'MaintenanceContractController@modify')->name('mc.modify');
			Route::post('modify', 'MaintenanceContractController@update_contract');
			Route::get('delete', 'MaintenanceContractController@delete')->name('mc.delete');
			Route::get('renew', 'MaintenanceContractController@renew')->name('mc.renew');
			Route::post('renew', 'MaintenanceContractController@dorenew');
			Route::get('et_mail', 'MaintenanceContractController@mail')->name('mc.et_mail');
			Route::get('es_mail', 'MaintenanceContractController@mail')->name('mc.es_mail');
			Route::get('je_mail', 'MaintenanceContractController@mail')->name('mc.je_mail');
			Route::get('ex_mail', 'MaintenanceContractController@mail')->name('mc.ex_mail');
		});
		Route::get('/', 'MaintenanceContractController@index')->name('mc.index');
		Route::get('sc', 'MaintenanceContractController@search_customer')->name('mc.sc');
		Route::get('new', 'MaintenanceContractController@new_contract')->name('mc.new');
		Route::post('new', 'MaintenanceContractController@store_contract');
		Route::get('es', 'MaintenanceContractController@expiring_soon')->name('mc.es');
		Route::get('iac', 'MaintenanceContractController@inactive')->name('mc.inactive');
		Route::get('jexp', 'MaintenanceContractController@just_expired')->name('mc.jexp');
		Route::get('upcoming', 'MaintenanceContractController@upcoming')->name('mc.upcoming');
		Route::get('details', 'MaintenanceContractController@details')->name('mc.details');
		Route::get('req', 'MaintenanceContractController@renew_req')->name('mc.renew_req');
		Route::get('contract', 'MaintenanceContractController@contract_req')->name('mc.contract_req');
	});
});

//Customer Print Object
Route::group(["middleware"	=>	["rolecheck:customerprintobject"]],function(){
	Route::prefix('cpo')->group(function(){
		Route::prefix('{code}')->group(function(){
			Route::get('details', 'CustomerPrintObjectController@details')->name('cpo.details');
			Route::get('download', 'CustomerPrintObjectController@download')->name('cpo.download');
			Route::get('activate', 'CustomerPrintObjectController@activate')->name('cpo.activate');
			Route::get('mail', 'CustomerPrintObjectController@mail')->name('cpo.mail');
			Route::get('preview', function(){ return view('cpo.preview'); })->name('cpo.preview');
			Route::post('preview', 'CustomerPrintObjectController@preview');
		});
		Route::get('previews', function(){ return view('cpo.previews'); })->name('cpo.previews');
		Route::get('create', 'CustomerPrintObjectController@create')->name('cpo.create');
		Route::post('create', 'CustomerPrintObjectController@store');
		Route::get('/', 'CustomerPrintObjectController@index')->name('cpo.index');
	});
});

//Distributor Branding
Route::group(["middleware"	=>	["rolecheck:distributorbranding"]],function(){
	Route::prefix('db')->group(function(){
		Route::get('{brand}/view', 'DistributorBrandingController@view')->name('db.view');
		Route::get('{brand}/add_domain', 'DistributorBrandingController@add_domain')->name('db.add_domain');
		Route::post('{brand}/add_domain', 'DistributorBrandingController@domain_add');
		Route::get('{brand}/edit', 'DistributorBrandingController@edit')->name('db.edit');
		Route::post('{brand}/edit', 'DistributorBrandingController@update');
		Route::get('{brand}/delete', 'DistributorBrandingController@delete')->name('db.delete');
		Route::post('{brand}/delete', 'DistributorBrandingController@destroy');
		Route::get('form', 'DistributorBrandingController@form')->name('db.new');
		Route::post('form', 'DistributorBrandingController@submit');
		Route::get('/', 'DistributorBrandingController@index')->name('db.index');
	});
});

//General Uploads
Route::group(["middleware"	=>	["rolecheck:generaluploads"]],function(){
	Route::prefix('gu')->group(function(){
		Route::get('create', 'GeneralUploadController@form')->name('gu.form');
		Route::post('create', 'GeneralUploadController@store');
		Route::prefix('{code}')->group(function(){
			Route::get('details', 'GeneralUploadController@details')->name('gu.details');
			Route::get('edit', 'GeneralUploadController@edit')->name('gu.edit');
			Route::post('edit', 'GeneralUploadController@update');
			Route::get('delete', 'GeneralUploadController@delete')->name('gu.delete');
			Route::get('drop', 'GeneralUploadController@drop')->name('gu.drop');
		});
		Route::get('/', 'GeneralUploadController@index')->name('gu.index');
	});
});

//Database Backups
Route::group(["middleware"	=>	["rolecheck:databasebackup"]],function(){
	Route::prefix('dbb')->group(function(){
		Route::get('/', 'DatabaseBackupController@index')->name('dbb.index');
		Route::get('upload', function(){ return view('dbb.upload'); })->name('dbb.upload');
		Route::post('upload', 'DatabaseBackupController@upload');
	});
});

//Service Request
Route::group(["middleware"	=>	["rolecheck:servicerequest"]],function(){
	Route::prefix('sreq')->group(function(){
		Route::prefix('{sr}')->group(function(){
			Route::get('edit', function(\App\Models\ServiceRequest $sr){ return view('sreq.form',compact('sr')); })->name('sreq.edit');
			Route::post('edit', 'ServiceRequestController@edit');
			Route::get('delete', function(\App\Models\ServiceRequest $sr){ return view('sreq.delete',compact('sr')); })->name('sreq.delete');
			Route::post('delete', 'ServiceRequestController@delete');
			Route::get('respond', function(\App\Models\ServiceRequest $sr){ return view('sreq.respond',compact('sr')); })->name('sreq.respond');
			Route::post('respond', 'ServiceRequestController@respond');
			Route::get('response', function(\App\Models\ServiceRequest $sr){ return view('sreq.respond',compact('sr')); })->name('sreq.response');
			Route::post('response', 'ServiceRequestController@response');
		});
		Route::get('view_all', function(){ return view('sreq.view_all'); })->name('sreq.view_all');
		Route::get('add', function(){ return view('sreq.form'); })->name('sreq.add');
		Route::post('add', 'ServiceRequestController@add');
		Route::get('/', 'ServiceRequestController@index')->name('sreq.index');
	});
});

//Ticket Category Master
Route::group(["middleware"	=>	["rolecheck:ticketcategorymaster"]],function(){
	Route::prefix('tcm')->group(function(){
		Route::prefix('{tcm}')->group(function(){
			Route::get('edit', function($tcm){ $tcm = \App\Models\TicketCategoryMaster::withoutGlobalScope('own')->find($tcm); return view('tcm.form',compact('tcm')); })->name('tcm.edit');
			Route::post('edit', 'TicketCategoryMasterController@edit');
			Route::get('delete', function($tcm){ $tcm = \App\Models\TicketCategoryMaster::withoutGlobalScope('own')->find($tcm); return view('tcm.delete',compact('tcm')); })->name('tcm.delete');
			Route::post('delete', 'TicketCategoryMasterController@delete');
			Route::get('specs', function($tcm){ $tcm = \App\Models\TicketCategoryMaster::withoutGlobalScope('own')->find($tcm); return view('tcm.specs',compact('tcm')); })->name('tcm.specs');
			Route::post('specs', 'TicketCategoryMasterController@specs');
		});
		Route::get('add', function(){ return view('tcm.form'); })->name('tcm.add');
		Route::post('add', 'TicketCategoryMasterController@add');
		Route::get('inactive', function(){ return view('tcm.index',['I' => true]); })->name('tcm.activate');
		Route::get('/', function(){ return view('tcm.index'); })->name('tcm.index');
	});
});

//Ticket Category Specification
Route::group(["middleware"	=>	["rolecheck:ticketcategorymaster"]],function(){
	Route::prefix('tcs')->group(function(){
		Route::prefix('{tcs}')->group(function(){
			Route::get('edit', function($tcs){ $tcs = \App\Models\TicketCategorySpecification::withoutGlobalScope('spec')->find($tcs); return view('tcs.form',compact('tcs')); })->name('tcs.edit');
			Route::post('edit', 'TicketCategorySpecificationController@edit');
			Route::get('delete', function($tcs){ $tcs = \App\Models\TicketCategorySpecification::withoutGlobalScope('spec')->find($tcs); return view('tcs.delete',compact('tcs')); })->name('tcs.delete');
			Route::post('delete', 'TicketCategorySpecificationController@delete');
			Route::get('activate', 'TicketCategorySpecificationController@activate')->name('tcs.activate');
		});
		Route::get('add', function(){ return view('tcs.form'); })->name('tcs.add');
		Route::post('add', 'TicketCategorySpecificationController@add');
		Route::get('/', function(){ return view('tcs.index'); })->name('tcs.index');
	});
});

//SMS Gateways
Route::group(["middleware"	=>	["rolecheck:smsgateway"]],function(){
	Route::prefix('smsg')->group(function(){
		Route::prefix('{code}')->group(function(){
			Route::get('edit', function(){ return view('smsg.form'); })->name('smsg.edit');
			Route::post('edit', 'SMSGatewayController@update');
			Route::get('inactivate', function(){ return view('smsg.inactivate'); })->name('smsg.inactivate');
			Route::post('inactivate', 'SMSGatewayController@inactivate');
		});
		Route::get('add', function(){ return view('smsg.form'); })->name('smsg.add');
		Route::post('add', 'SMSGatewayController@store');
		Route::get('/', function(){ return view('smsg.index'); })->name('smsg.index');
	});
});

//Mail
Route::group(["middleware"	=>	["rolecheck:mail"]],function(){
	Route::prefix('mail')->group(function(){
		Route::prefix('{code}')->group(function(){
			Route::get("edit",function(){ return view('mail.compose'); })->name("mail.edit");
			Route::post("edit","MailController@update");
			Route::get("send",function(){ return view('mail.send'); })->name("mail.send");
			Route::post("send","MailController@send");
			Route::get("report",function(){ return view('mail.report'); })->name("mail.report");
		});
		Route::get("compose",function(){ return view('mail.compose'); })->name("mail.compose");
		Route::post("compose","MailController@compose")->name("mail.compose");
		Route::get("/",function(){ return view('mail.index'); })->name("mail.index");
	});
});

//Public Articles
Route::group(["middleware"	=>	["rolecheck:publicarticle"]],function(){
	Route::prefix('pa')->group(function(){
		Route::prefix('{code}')->group(function(){
			Route::get("edit",function(){ return view('pa.form'); })->name("pa.edit");
			Route::post("edit","PublicArticleController@update")->name("pa.edit");
		});
		Route::get("new",function(){ return view('pa.form'); })->name("pa.new");
		Route::post("new","PublicArticleController@store");
		Route::get("/",function(){ return view('pa.index'); })->name("pa.index");
	});
});

//Notifications
Route::group(["middleware"	=>	["rolecheck:notifications"]],function(){
	Route::get("notification/list",function(){ return view('notification.list'); })->name("notification.list");
	Route::prefix('n')->group(function(){
		Route::prefix('{code}')->group(function(){
			Route::get("audience",function(){ return view('notification.audience'); })->name("notification.audience");
			Route::post("audience","NotificationController@audience");
			Route::get("edit",function(){ return view('notification.form'); })->name("notification.edit");
			Route::post("edit","NotificationController@update");
			Route::get("preview",function(){ return view('notification.preview'); })->name("notification.preview");
			Route::get("report","NotificationController@report")->name("notification.report");
		});
		Route::get("new",function(){ return view('notification.form'); })->name("notification.new");
		Route::post("new","NotificationController@store");
		Route::get("/",function(){ return view('notification.index'); })->name("notification.index");
	});
});

//Private Articles
Route::group(["middleware"	=>	["rolecheck:privatearticles"]],function(){
	Route::prefix('pra')->group(function(){
		Route::prefix('{code}')->group(function(){
			Route::get("audience",function(){ return view('pra.audience'); })->name("pra.audience");
			Route::post("audience","PrivateArticleController@audience");
			Route::get("edit",function(){ return view('pra.form'); })->name("pra.edit");
			Route::post("edit","PrivateArticleController@update")->name("pra.edit");
			Route::get("preview",function(\App\Models\PrivateArticle $code){ return view('pra.articles.'.$code->view); })->name("pra.preview");
			Route::get("report","PrivateArticleController@report")->name("pra.report");
		});
		Route::get("list",function(){ return view('pra.list'); })->name("pra.list");
		Route::get("new",function(){ return view('pra.form'); })->name("pra.new");
		Route::post("new","PrivateArticleController@store");
		Route::get("/",function(){ return view('pra.index'); })->name("pra.index");
	});
});

//Third Party Softwares
Route::group(["middleware"	=>	["rolecheck:thirdpartysoftwares"]],function(){
	Route::prefix('tpa')->group(function(){
		Route::prefix('{code}')->group(function(){
			Route::get("file",function(){ return view('tpa.file'); })->name("tpa.file");
			Route::post("file","ThirdPartyApplicationController@updatefile");
			Route::get("edit",function(){ return view('tpa.form'); })->name("tpa.edit");
			Route::post("edit","ThirdPartyApplicationController@update")->name("tpa.edit");
		});
		Route::get("new",function(){ return view('tpa.form'); })->name("tpa.new");
		Route::post("new","ThirdPartyApplicationController@store");
		Route::get("/",function(){ return view('tpa.index'); })->name("tpa.index");
	});
});

//Public Print Objects
Route::group(["middleware"	=>	["rolecheck:publicprintobjects"]],function(){
    Route::prefix('ppo')->group(function(){
        Route::prefix('{code}')->group(function(){
            Route::get("delete",function($code){ return view('ppo.delete',compact('code')); })->name("ppo.delete");
            Route::post("delete","PublicPrintObjectController@delete");
            Route::get("edit",function($code){ return view('ppo.edit',compact('code')); })->name("ppo.edit");
            Route::post("edit","PublicPrintObjectController@update");
            Route::get("change_preview",function($code){ return view('ppo.change_preview',compact('code')); })->name("ppo.change_preview");
            Route::post("change_preview","PublicPrintObjectController@preview");
            Route::get("change_file",function($code){ return view('ppo.change_file',compact('code')); })->name("ppo.change_file");
            Route::post("change_file","PublicPrintObjectController@file");
            Route::get("download","PublicPrintObjectController@download")->name("ppo.download");
            Route::get("view",function($code){ return view('ppo.view',compact('code')); })->name("ppo.view");
        });
        Route::get("new",function(){ return view('ppo.new'); })->name("ppo.new");
        Route::post("new","PublicPrintObjectController@store");
        Route::get("/",function(){ return view('ppo.index'); })->name("ppo.index");
    });
});

//Vacancies
Route::group(["middleware"	=>	["rolecheck:vacancy"]],function(){
    Route::prefix('vacancy')->group(function(){
        Route::get("download/{Applicant}",'VacancyController@download')->name("vacancy.resume.download");
        Route::prefix('{code}')->group(function(){
            Route::get("details",function($code){ return view('vacancy.detail',['code'=>$code]); })->name("vacancy.details");
            Route::get("on",'VacancyController@on')->name("vacancy.on");
            Route::get("off",'VacancyController@off')->name("vacancy.off");
        });
        Route::get("create",function(){ return view('vacancy.create'); })->name("vacancy.create");
        Route::post("create",'VacancyController@store');
        Route::get("manage",function(){ return view('vacancy.index'); })->name("vacancy.manage");
    });
});
Route::get('/vacancy/apply/{code}', function($code){ return view('vacancy.apply',['code' => $code]); })->name('vacancy.apply');
Route::post('/vacancy/apply/{Vacancy}', 'VacancyController@apply');
Route::post('/vacancy','VacancyController@PreVacancyNotify');

//FAQ
Route::group(["middleware"	=>	["rolecheck:faq"], "prefix" => "faq"],function(){
    Route::prefix('{id}')->group(function(){
        Route::get('view', function(){ return view('faq.view'); })->name('faq.view');
        Route::get('edit', function(){ return view('faq.edit'); })->name('faq.edit');
        Route::post('edit', 'FAQController@update');
        Route::get('scope', function(){ return view('faq.scope'); })->name('faq.scope');
        Route::post('scope', 'FAQController@scope');
        Route::get('product', function(){ return view('faq.product'); })->name('faq.product');
        Route::post('product', 'FAQController@product');
        Route::get('category', function(){ return view('faq.category'); })->name('faq.category');
        Route::post('category', 'FAQController@category');
        Route::get('delete', 'FAQController@delete')->name('faq.delete');
        Route::get('undelete', 'FAQController@undelete')->name('faq.undelete');
    });
    Route::get('create', function(){ return view('faq.create'); })->name('faq.create');
    Route::post('create', 'FAQController@create');
    Route::get('index', function(){ return view('faq.index'); })->name('faq.index');
    Route::get('list', function(){ return view('faq.list'); })->name('faq.list');
});
