<?php

namespace Moduloom\PublicInformation\Http\Controllers;

use Illuminate\Routing\Controller;
use Moduloom\PublicInformation\Models\Notice;

class PublicNoticesController extends Controller
{
    public function index()
    {
        $notices = Notice::query()->latest('published_at')->get();

        return view('public-information::notices', ['notices' => $notices]);
    }
}
