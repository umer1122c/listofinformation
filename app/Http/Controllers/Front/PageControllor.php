<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Media;
use App\Models\MediaCategory;
use App\Models\Contact;
use App\Models\Team;
use Session;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use URL;
use Validator;
use Illuminate\Support\Facades\Mail;

class PageControllor extends Controller
{
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }
    
    public function membership(){
        $data['title'] = 'Membership Categories';
        $data['description'] = '';
        $data['class'] = 'membership';
        $data['table'] = 'Membership Categories';
        return view('front/pages/membership' , $data);
    }

    public function programme(){
        $data['title'] = 'Programmes (Services)';
        $data['description'] = '';
        $data['class'] = 'programme';
        $data['table'] = 'Programmes (Services)';
        return view('front/pages/programme' , $data);
    }
    
    public function history(){
        $data['title'] = 'History of Globaboom';
        $data['description'] = 'History of Globaboom';
        $data['class'] = 'history';
        $data['table'] = 'History of Globaboom';
        
        return view('front/pages/view_history' , $data);
    }
    
    public function mvstatements(){
        $data['title'] = 'Mission And Vision Statement';
        $data['description'] = 'Mission And Vision Statement';
        $data['class'] = 'mvstatements';
        $data['table'] = 'Mission And Vision Statement';
        return view('front/pages/view_mvstatements' , $data);
    }
    
    public function organization(){
        $data['title'] = 'Organization of Globaboom';
        $data['description'] = '';
        $data['class'] = 'organization';
        $data['table'] = 'Organization of Globaboom';
        return view('front/pages/view_organization' , $data);
    } 
}