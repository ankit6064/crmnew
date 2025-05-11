<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;

class PostController extends Controller
{
    public function index()
    {
        // pr("ll");
        $sourcesWithLeads = Source::
            withCount('leads')
            ->limit(10)
            ->get();
        pr($sourcesWithLeads);
        // return view('dt');
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $data = Source::all();

            return DataTables::eloquent($data)
                ->addColumn('actions', function ($post) {
                    // Customize additional columns if needed
                    return '<button>Edit</button> <button>Delete</button>';
                })
                ->rawColumns(['actions'])
                ->toJson();
        }
    }
}
