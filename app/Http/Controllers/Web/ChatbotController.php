<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('pages.feature.chatbot');
    }
}
