<?php

namespace App\Http\Controllers;

class SmartSaleAssetController extends Controller
{
    private $fields = [
        'setup' => ['name,value','APP','download'],
        'product_transaction_natures' => ['name','APP','download'],
        'product_transaction_types' => ['name','APP','download'],
        'areas' => ['name','APP','download'],
        'stores' => ['name,cocode,co_abr,brcode,br_abr,currency','APP','download'],
        'users' => ['name,code,email,phone,address,outstanding,overdue','APP','both'],
        'area_users' => ['area,user','APPUSER','both'],
        'user_store_area' => ['user,store,area','APPUSER','download'],
        'settings' => ['name,description,value','APP','download'],
        'user_settings' => ['user,setting,value','APPUSER','both'],
        'fiscalyearmaster' => ['code,name,abr,start_date,end_date','APP','download'],
        'functiondetails' => ['code,format,digit_length,tax,taxselection,list,ratewithtax,discount01,discount02,discount02base,discount03,discountmode,discount','APP','download'],
        'product_group_master' => ['name,code,list','APP','download'],
        'products' => ['name,uom,narration,taxcode01,taxfactor01,subtaxfactor01,taxcode02,taxfactor02,subtaxfactor02','APP','download'],
        'product_groups' => ['product,g01,g02,g03,g04,g05,g06,g07,g08,g09,g10','APP','download'],
        'pricelist_header' => ['name','APP','download'],
        'pricelist' => ['pricelist,product,price','APP','download'],
        'sales_order' => ['docno,date,user,customer,fycode,fncode,payment_type,progress,_ref','USER','both'],
        'sales_order_items' => ['so,product,rate,quantity,taxrule,tax,discount,total,_ref','USER','both'],
        'store_product_transactions' => ['store,product,direction,quantity,user,nature,date,type,_ref','USER','both'],
        'transactions' => ['user,docno,date,customer,fycode,fncode,payment_type,_ref','USER','both'],
        'transaction_details' => ['transaction,spt,amount,taxrule,tax,discount,total','USER','both'],
        'sales_order_sales' => ['so,product,quantity,transaction,sale_quantity','USER','both'],
        'receipts' => ['docno,fycode,fncode,mode,customer,date,user,amount,bank,cheque,cheque_date,_ref,status','USER','both'],
        'stock_transfer' => ['out,in,verified_by,verified_at','USER','both'],
        'fn_reserves' => ['fncode,user,store,start_num,end_num,quantity,current,progress,status','USER','both'],
    ];
    public function index(){
        return $this->fields;
    }
}
