<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    function formulir()
    {
        return view('formulir');
    }
    function inputData(Request $requestData)
    {
        $nama = $requestData->input('nama');
        $nim = $requestData->input('nim');
        $prodi = $requestData->input('prodi');
        
        return view('tampilkan', compact('nama', 'nim', 'prodi'));
    }
}
