<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatabaseBackupPackageController extends Controller
{
    public function Index(Request $request)
    {
        return view("dbBackupPackage.dbBackupPackageCreation");
    }
}
