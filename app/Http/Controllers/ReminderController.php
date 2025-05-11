<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use App\Models\Note;
use Auth;   
class ReminderController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function view()
    {
        $today = date('Y-m-d');
		$data =  Note::where(['user_id'=>auth()->user()->id,'reminder_date'=>$today])->with('lead')->get()->toarray(); 
		//print_r($data); die();
        return view('reminder.view')->with(['data'=>$data]);
    }
}
