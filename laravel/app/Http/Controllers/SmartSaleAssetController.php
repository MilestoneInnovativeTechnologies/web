<?php

namespace App\Http\Controllers;

class SmartSaleAssetController extends Controller
{
    private $fields = [
        'setup' => ['name,value','APP','download'],
        'menu' => ['fncode,category,category_display,order,icon,home_display,drawer_display,component,props,status','APP','download'],
        'areas' => ['name','APP','download'],
        'stores' => ['name,cocode,co_abr,brcode,br_abr,currency','APP','download'],
        'users' => ['name,code,email,login,phone,address,outstanding,overdue','APP','both'],
        'area_users' => ['area,user','APPUSER','both'],
        'user_store_area' => ['user,store,area','APPUSER','download'],
        'settings' => ['name,description,value','APP','download'],
        'user_settings' => ['user,setting,value','APPUSER','both'],
        'fiscalyearmaster' => ['code,name,abr,start_date,end_date','APP','download'],
        'functiondetails' => ['code,format,digit_length,pricelist,tax,taxselection,list,ratewithtax,discount01,discount02,discount02base,discount03,discountmode,discount','APP','download'],
        'product_group_master' => ['name,code,list','APP','download'],
        'products' => ['name,uom,narration,taxcode01,taxfactor01,subtaxfactor01,taxcode02,taxfactor02,subtaxfactor02','APP','download'],
        'product_groups' => ['product,g01,g02,g03,g04,g05,g06,g07,g08,g09,g10','APP','download'],
        'pricelist_header' => ['name','APP','download'],
        'pricelist' => ['pricelist,product,price','APP','download'],
        'sales_order' => ['docno,date,user,customer,fycode,fncode,payment_type,progress,_ref','USER','both'],
        'sales_order_items' => ['so,product,rate,quantity,taxrule,tax,discount01,discount02,total,_ref','USER','both'],
        'transactions' => ['user,docno,date,customer,store,fycode,fncode,payment_type,_ref','USER','both'],
        'transaction_details' => ['transaction,product,quantity,direction,store,taxrule,tax,discount01,discount02,soi','USER','both'],
        'receipts' => ['docno,fycode,fncode,mode,customer,date,user,amount,bank,cheque,cheque_date,_ref,status','USER','both'],
        'stock_transfer' => ['out,in,verified_by,verified_at','USER','both'],
        'fn_reserves' => ['fncode,user,store,start_num,end_num,quantity,current,progress,status','USER','both'],
    ];
    private $menu = [
        //'NAME' => ['ComponentName','props<coma separated>','FNCodes<coma separated>','status']
        'PURCHASE' => ['','fncode,store,fycode','PUR1,PUR2,PUR3,PUR4,PUR5','Inactive'],
        'PURCHASE RETURN' => ['','fncode,store,fycode','PR1,PR2,PR3','Inactive'],
        'PURCHASE ORDER' => ['','fncode,store,fycode','PO1','Inactive'],
        'PAYMENT' => ['','fncode,store,fycode','CP1,BP1,BP2','Inactive'],
        'SALES' => ['SalesIndex','fncode,store,fycode','SL1,SL2,SL3,SL4,SL5','Active'],
        'SALES RETURN' => ['SalesReturnIndex','fncode,store,fycode','SR1,SR2,SR3','Active'],
        'SALES ORDER' => ['SalesOrderIndex','fncode,store,fycode','SO1,SO2','Active'],
        'RECEIPT' => ['ReceiptIndex','fncode,store,fycode','CR1,BR1,BR2','Active'],
        'MATERIAL TRANSFER' => ['MaterialTransferIndex','fncode,store,fycode','MT1,MT2','Active'],
        'BRANCH TRANSFER' => ['','fncode,store,fycode','MT3,MT4','Inactive'],
    ];
    public function index(){
        return ['db' => $this->fields, 'menu' => $this->menu];
    }
}
