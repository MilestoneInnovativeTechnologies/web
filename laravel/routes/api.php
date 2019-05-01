<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// If the requesting role have the resource, mentioned in wapi are allowed..


Route::get("countries","GeoLocationApi@AllCountries");
Route::get("industries","CustomerController@industries");
Route::get("{Country}/states","GeoLocationApi@States");
Route::get("{State}/cities","GeoLocationApi@Cities");
Route::post("sdl","HomeController@sdl");
Route::get("features/{pid}","HomeController@features");
Route::get("appinit/entry","AppInitController@entry");
Route::get("appinit/{key}","AppInitController@init");
Route::post("ifv","FAQController@ifv");
Route::post("ifb","FAQController@ifb");

Route::prefix("keycode")->group(function(){
    Route::get('encode', 'KeyCodeEncDecController@encode');
    Route::get('decode/{code?}', 'KeyCodeEncDecController@decode')->name('keycode.decode');
});

Route::prefix("v1")->group(function(){
	
	Route::group(["middleware"	=>	"wapi:*"],function(){
		Route::post("L2D","LicenceToData@upload");
		Route::get("get_packages","ProductInformationController@packages");
		Route::get("get_download_link","ProductInformationController@download_link");
		Route::get("partner/get/dcs","PartnerController@detail_search");
	});
	
	Route::group(["middleware"	=>	"wapi:customers,dealers"],function(){
		Route::get("countries","GeoLocationApi@Countries");
		Route::get('{Customer}/resetlogin',"CustomerController@resetlogin");
		Route::get('customer/get/dcs','CustomerController@detail_search');
	});
	
	Route::group(["middleware"	=>	"wapi:customers"],function(){
		Route::get("{Customer}/{Product}/editions","CustomerController@editions");
		Route::get("email/unique/{email}","CustomerController@uniqueemailcheck");
		Route::get("{product}/editions","CustomerController@editions");
		Route::get("{Customer}/products","CustomerController@products");
		Route::get("products","CustomerController@products");
		Route::get("dealers","CustomerController@dealers");
		Route::prefix("customer")->group(function(){
			Route::get("list/{page}/{items}","CustomerController@lists");
			Route::get("unique/{name}","CustomerController@uniquenamecheck");
		});
	});
	
	Route::group(["middleware"	=>	"wapi:customerdashboard"],function(){
		Route::prefix("customer")->group(function(){
			Route::get("reginfo/{seqno}","CustomerPage@reginfo");
			Route::get("packages","CustomerPage@packages");
		});
	});
	
	Route::group(["middleware"	=>	"wapi:dealerdashboard"],function(){
		Route::prefix("dealer")->group(function(){
			Route::get("myproducts","DealerController@myproducts");
			Route::get("mydetails","DealerController@mydetails");
			Route::get("mycustomers","DealerController@mycustomers");
			Route::get("myparent","DealerController@myparent");
		});
	});
	
	Route::group(["middleware"	=>	"wapi:dealers"],function(){
		Route::prefix("dealer")->group(function(){
			Route::get("list/{page}/{items?}","DealerController@lists");
			Route::get("{dealer}","DealerController@dealer");
			Route::get('get/dds','DealerController@detail_search');
		});
	});
	
	Route::group(["middleware"	=>	"wapi:distributordashboard"],function(){
		Route::prefix("dd")->group(function(){
			Route::get("content","DistributorController@content");
			Route::get("transactions","DistributorController@transactions");
			Route::get("dealer/{dealer}","DealerController@report");
			Route::get("sidl","ProductInformationController@sidl");
			Route::get("sputc","ProductInformationController@sputc");
		});
	});
	
	Route::group(["middleware"	=>	"wapi:distributordashboard,dealerdashboard,supportteam,supportagent"],function(){
		Route::get("my_customers","ProductInformationController@my_customers");
		Route::get("get_my_product_customers","ProductInformationController@my_product_customers");
		Route::get("vpd","ProductInformationController@vpd");
	});
	
	Route::group(["middleware"	=>	"wapi:distributors"],function(){
		Route::prefix("distributor")->group(function(){
			Route::get("list/{page}/{items?}","DistributorController@lists");
			Route::get("{distributor}","DistributorController@distributor");
			Route::get('get/dds','DistributorController@detail_search');
		});
	});
	
	Route::group(["middleware"	=>	"wapi:pricelist"],function(){
			Route::get("pricelist/all","PriceListController@apigetall");
	});

	Route::group(["middleware"	=>	"wapi:company"],function(){
		Route::prefix("mit")->group(function(){
			Route::get("customer/products/{items?}/{page?}","CompanyController@getCustomerProducts");
			Route::get("partner/{partner}/products/{items?}/{page?}","CompanyController@getPartnerProducts");
			Route::get("distributor/{distributor}/dealers/{items?}/{page?}","CompanyController@getDealersCustomers");
			Route::get("{distributor}/transactions","CompanyController@transactions");
			Route::prefix("list")->group(function(){
				Route::get("dealer/{dst}","CompanyController@dealer_list");
				Route::get("customer/{dlr}","CompanyController@customer_list");
				Route::get("distributor","CompanyController@distributor_list");
			});
			Route::prefix("exec")->group(function(){
				Route::get("updateprice/{code}/{param}","TransactionController@apiUpdatePrice");
				Route::get("alterstatus/{code}","TransactionController@alterStatus");
			});
			Route::prefix("action")->group(function(){
				Route::get("slsl/{Partner}","PartnerController@slsl");
				Route::get("udst","CompanyController@udst");
				Route::post("sum","ProductUpdateMailer@sum");
			});
			Route::prefix("get")->group(function(){
				Route::get("{product}/{edition}/{package}/PVD","ProductUpdateMailer@pvd");
				Route::get("{product}/{edition}/packages","ProductUpdateMailer@getpackages");
				Route::get("{product}/{edition}/distributors","ProductUpdateMailer@getdistributors");
				Route::get("distributor/{distributor}/supportteam","CompanyController@dist_st");
				Route::get("{product}/editions","ProductUpdateMailer@geteditions");
				Route::get("customer/search","ProductUpdateMailer@search");
				Route::get("supportteams","CompanyController@sts");
			});
		});
	});

	Route::group(["middleware"	=>	"wapi:company"],function(){
		Route::prefix("capi")->group(function(){
			Route::prefix("get")->group(function(){
				Route::get("00grr","CompanyAPIController@get_registration_requests");
				Route::get("gurrc","CompanyAPIController@get_unregistered_recent_customers");
				Route::get("00dec","CompanyAPIController@get_expiring_customers");
				Route::get("00rrc","CompanyAPIController@recently_registered_customers");
				Route::get("00prs","CompanyAPIController@product_registration_summary");
				Route::get("000ts","CompanyAPIController@get_ticket_summary");
				Route::get("00cat","CompanyAPIController@current_active_tickets_summary");
				Route::get("000ps","CompanyAPIController@partner_search");
				Route::get("0stkt","CompanyAPIController@ticket_search");
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:log"],function(){
		Route::prefix('log')->group(function(){
			Route::prefix('unusr')->group(function(){
				Route::get('map/search','AppLogController@searchcustomer');
				Route::get('ignore','AppLogController@ignore');
				Route::get('ignored','AppLogController@ignored');
				Route::get('mapped','AppLogController@mapped');
				Route::get('map','AppInitController@map');
				Route::get('/','AppLogController@getunknownuserdata');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:supportteam"],function(){
		Route::prefix('tst')->group(function(){
			Route::prefix('action')->group(function(){
				Route::get('ud/{code}','SupportTeamController@update_distributors');
				Route::get('stuc/{code}','SupportTeamController@update_customers');
			});
			Route::prefix('get')->group(function(){
				Route::get('dc','SupportTeamController@get_dist_customers');
				Route::get('tds','SupportTeamController@detail_search');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:supportagent"],function(){
		Route::prefix('tsa')->group(function(){
			Route::prefix('get')->group(function(){
				Route::get('tds','TechnicalSupportAgentController@detail_search');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:supportteampage"],function(){
		Route::prefix('tst')->group(function(){
			Route::prefix('action')->group(function(){
				Route::get('sdlrl','SupportTeamPageController@distributor_resetlogin');
				Route::get('sclrl','SupportTeamPageController@customer_resetlogin');
				Route::get('spi','SupportTeamPageController@send_product_information');
				Route::get('spum','SupportTeamPageController@send_product_update');
				Route::get('upd','SupportTeamPageController@update_presale_dates');
				Route::get('uptc','SupportTeamPageController@update_product_perm_cats');
			});
			Route::prefix('get')->group(function(){
				Route::get('dstprd','SupportTeamPageController@get_distributor_products');
				Route::get('dce','SupportTeamPageController@get_distributor_customers_email');
				Route::get('pvd','SupportTeamPageController@get_latest_package_version');
				Route::get('dpece','SupportTeamPageController@get_dist_prod_edit_cust');
				Route::get('pi','SupportTeamPageController@get_prod_info');
				Route::get('psd','SupportTeamPageController@get_presale_dates');
				Route::get('dstcnt','SupportTeamPageController@get_dist_countries');
				Route::get('dstdlr','SupportTeamPageController@get_dist_dealers');
				Route::get('packages','SupportTeamPageController@get_packages');
				Route::get('pntc','SupportTeamPageController@get_product_and_perm_cats');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:customercookie"],function(){
		Route::prefix('tscc')->group(function(){
			Route::prefix('action')->group(function(){
				Route::get('ac','CustomerCookieController@add_cookie');
				Route::get('rc','CustomerCookieController@remove_cookie');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:remoteconnection"],function(){
		Route::prefix('crc')->group(function(){
			Route::prefix('action')->group(function(){
				Route::get('ac','CustomerRemoteConnectionController@add_connection');
				Route::get('rc','CustomerRemoteConnectionController@remove_connection');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:ticket"],function(){
		Route::prefix('tkt')->group(function(){
			Route::prefix('action')->group(function(){
				Route::get('tkt/{tkt}/tsk/create','TicketController@create_ticket_task');
				Route::prefix('{user}/{tkt}')->group(function(){
					Route::post('ucf','TicketCoversationController@upload_conv_file');
					Route::get('scv','TicketCoversationController@store_chat_conversation');
					Route::get('sdlc','TicketCoversationController@send_download_link_chat');
					Route::get('sdlm','TicketCoversationController@send_download_link_mail');
					Route::post('apo','TaskWorkPageController@add_print_object');
					Route::get('spoc','TicketCoversationController@send_print_object_chat');
					Route::get('spom','TicketCoversationController@send_print_object_mail');
					Route::get('cguf','TaskWorkPageController@create_upload_form');
					Route::get('cgfu','TaskWorkPageController@check_general_file_uploaded');
					Route::get('cufo','TaskWorkPageController@change_generalupload_overwrite');
					Route::get('dguf','TaskWorkPageController@drop_generalupload_file');
					Route::get('dgf','TaskWorkPageController@delete_generalupload');
					Route::get('gufc','TicketCoversationController@chat_generalupload_form');
					Route::get('mfl','TicketCoversationController@mail_generalupload_form');
					Route::get('dguff','TicketCoversationController@mail_generalupload_file');
					Route::get('sct','TaskWorkPageController@send_chat_transcript');
					Route::get('stpadl','TicketCoversationController@send_thirdparty_downloadlink');
				});
				Route::get('{tkt}/uw','TicketController@update_weightages');
				Route::get('cck','TaskWorkPageController@create_customer_cookie');
				Route::get('rck','TaskWorkPageController@remove_customer_cookie');
				Route::get('ccrc','TaskWorkPageController@create_customer_connection');
				Route::get('rcrc','TaskWorkPageController@remove_customer_connection');
			});
			Route::prefix('get')->group(function(){
				Route::prefix('{user}/{tkt}')->group(function(){
					Route::get('gac','TicketCoversationController@get_all_chat_conversation');
					Route::get('glc','TicketCoversationController@get_latest_conversation');
					Route::get('gfn','TaskWorkPageController@get_customer_product_po_functions');
					Route::get('gpn','CustomerPrintObjectController@get_print_names');
					Route::get('poh','TaskWorkPageController@get_print_object_history');
					Route::get('tu','TaskWorkPageController@get_ticket_users');
				});
				Route::get('td/{tkt}','TicketController@get_ticket_details');
				Route::get('mp','TicketController@get_my_products');
				Route::get('cp','TicketController@get_customer_products');
				Route::get('dc','TicketController@get_distributor_customers');
				Route::get('sc','TicketController@get_sub_categories');
				Route::get('sp','TicketController@get_support_types');
				Route::get('hat','TicketController@get_tasks_for_handle_after');
				Route::get('tau','TicketController@get_ticket_assignable_users');
				Route::get('prs','TicketController@get_ticket_progress');
				Route::get('atc','TicketCategoryMasterController@get_reg_categories');
				Route::get('ccat','TicketCategoryMasterController@get_customer_categories');
				Route::get('cspec','TicketCategoryMasterController@get_category_specs');
				Route::get('fgcat','TicketCategoryMasterController@force_get_category');
				Route::get('scu','TicketController@search_customer');
				Route::get('src','CustomerController@search');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:maintenancecontract"],function(){
		Route::prefix('mc')->group(function(){
			Route::prefix('get')->group(function(){
				Route::get('sc','MaintenanceContractController@search_for_customer');
			});
			Route::prefix('action')->group(function(){
				Route::prefix('sm')->group(function(){
					Route::get('{mail}/{code}','MaintenanceContractController@send_mail');
				});
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:customerprintobject"],function(){
		Route::prefix('cpo')->group(function(){
			Route::prefix('get')->group(function(){
				Route::get('sc','CustomerPrintObjectController@search_for_customer');
				Route::get('pn','CustomerPrintObjectController@get_print_names');
			});
			Route::prefix('action')->group(function(){
				Route::prefix('sm')->group(function(){});
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:generaluploads"],function(){
		Route::prefix('gu')->group(function(){
			Route::prefix('action')->group(function(){
				Route::get('sfl','GeneralUploadController@send_formlink_email');
				Route::get('sff','GeneralUploadController@send_formfile_email');
			});
			Route::prefix('get')->group(function(){
				Route::get('sc','CustomerController@search');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:databasebackup"],function(){
		Route::prefix('dbb')->group(function(){
			Route::prefix('get')->group(function(){
				Route::get('sc','CustomerController@search');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:mail"],function(){
		Route::prefix('mail')->group(function(){
			Route::prefix('get')->group(function(){
				Route::get('dcs','CustomerController@detail_search');
				Route::get('dds','DistributorController@detail_search');
				Route::get('log','MailController@log');
			});
		});
	});
	
	Route::group(["middleware"	=>	"wapi:notification"],function(){
		Route::prefix('n')->group(function(){
			Route::prefix('get')->group(function(){
				Route::get('dcs','CustomerController@detail_search');
				Route::get('dds','DistributorController@detail_search');
				Route::get('log','MailController@log');
			});
		});
	});

	Route::group(["middleware"	=>	"wapi:*"],function(){
	    Route::get('faq/get/prt','FAQController@prt');
	    Route::post('faq/addAppLogController/fct','FAQController@fct');
	});

	
});

Route::group(["prefix" => "pd", "middleware" => "\\App\\Http\\Middleware\\Cors"],function(){
    Route::get('/','PDController@api');
    Route::get('interact/{code}','PDController@interact');
    Route::get('interact/update/{code}','PDController@intupd');
});

Route::group(["prefix" => "ss", "middleware" => "\\App\\Http\\Middleware\\Cors"],function(){
    Route::group(["prefix" => "sync/table"],function(){
        Route::get('info','SmartSaleController@apiTableInfo');
        Route::get('{id}/set','SmartSaleController@apiTableSet');
    });
    Route::get('interact/{code}','PDController@interact');
    Route::get('interact/update/{code}','PDController@intupd');
});
