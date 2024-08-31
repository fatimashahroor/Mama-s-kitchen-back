<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Additional_ingController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:additional_ing-list');
         $this->middleware('permission:additional_ing-create', ['only' => ['create','store']]);
         $this->middleware('permission:additional_ing-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:additional_ing-delete', ['only' => ['destroy']]);
    }
}
