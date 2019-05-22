<?php

namespace App\Http\Controllers;

class SmartSaleAssetController extends Controller
{
    private $fields = [
        'area_users' => ['area,user','APPUSER','3',''],
        'areas' => ['name','APP','6',''],
        'fiscalyearmaster' => ['name,start_date,end_date','APP','24',''],
        'functiondetails' => ['format,digit_length','APP','18',''],
        'pricelist' => ['name','APP','24',''],
        'pricelist_products' => ['pricelist,product,price','APP','24',''],
        'product_transaction_natures' => ['name','APP','24',''],
        'product_transaction_types' => ['name','APP','24',''],
        'productgroups' => ['name,belongs,parent,tax1,tax2','APP','24',''],
        'products' => ['name,uom,group1,group2,group3,group4','APP','12',''],
        'sales_order' => ['docno,date,user,customer,fycode,fncode,progress','USER','1','10'],
        'sales_order_items' => ['so,product,rate,quantity','USER','1','10'],
        'settings' => ['name,description,value','APP','24',''],
        'stock_transfer' => ['out,in,verified_by,verified_at','APPUSER','3',''],
        'store_product_transactions' => ['store,product,direction,quantity,user,nature,date,type','USER','1','5'],
        'stores' => ['name','APP','12',''],
        'transaction_details' => ['transaction,spt,price,tax,discount,total','USER','1','5'],
        'transactions' => ['user,docno,date,customer,fycode,fncode','USER','1','5'],
        'user_settings' => ['user,setting,value','APPUSER','12',''],
        'user_store_area' => ['user,store,area','APPUSER','3',''],
        'users' => ['name','APP','6','']
    ];
    public function index(){
        return $this->fields;
    }
}
