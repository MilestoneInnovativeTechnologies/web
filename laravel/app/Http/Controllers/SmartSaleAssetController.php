<?php

namespace App\Http\Controllers;

class SmartSaleAssetController extends Controller
{
    private $fields = [
        'setup' => ['name,value','APP','24',''],
        'area_users' => ['area,user','APPUSER','3',''],
        'areas' => ['name','APP','6',''],
        'fiscalyearmaster' => ['code,name,abr,start_date,end_date','APP','24',''],
        'functiondetails' => ['code,format,digit_length','APP','18',''],
        'pricelist_header' => ['name','APP','24',''],
        'pricelist' => ['pricelist,product,price','APP','24',''],
        'product_transaction_natures' => ['name','APP','24',''],
        'product_transaction_types' => ['name','APP','24',''],
        'products' => ['name,uom,narration,group1,group2,group3,group4','APP','12',''],
        'sales_order' => ['docno,date,user,customer,fycode,fncode,payment_type,progress,_ref','USER','1','1'],
        'sales_order_items' => ['so,product,rate,quantity,tax,discount,total,_ref','USER','1','1'],
        'settings' => ['name,description,value','APP','24',''],
        'stores' => ['name,cocode,co_abr,brcode,br_abr','APP','12',''],
        'store_product_transactions' => ['store,product,direction,quantity,user,nature,date,type,_ref','USER','1','1'],
        'transactions' => ['user,docno,date,customer,fycode,fncode,payment_type,_ref','USER','1','1'],
        'transaction_details' => ['transaction,spt,amount,tax,discount,total','USER','1','1'],
        'stock_transfer' => ['out,in,verified_by,verified_at','USER','3','1'],
        'user_settings' => ['user,setting,value','APPUSER','12',''],
        'user_store_area' => ['user,store,area','APPUSER','3',''],
        'users' => ['name,code,email,phone,address,outstanding_normal,outstanding_overdue,outstanding_critical','USER','1','1'],
        'receipts' => ['docno,fycode,fncode,mode,customer,date,user,amount,bank,cheque,cheque_date,_ref,status','USER','3','1'],
        'fn_reserves' => ['fncode,user,store,start_num,end_num,quantity,current,progress,status','USER','2','1'],
        'sales_order_sales' => ['so,product,quantity,transaction,sale_quantity','USER','1','1'],
    ];
    public function index(){
        return $this->fields;
    }
}
