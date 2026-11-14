<?php

namespace Moduloom\PublicInformation\Http\Controllers;

use App\Kernel\ThemeManager;
use Illuminate\Routing\Controller;
use Moduloom\PublicInformation\Models\Notice;

class PublicNoticesController extends Controller
{
    public function index(ThemeManager $themes)
    {
        $notices = Notice::query()->latest('published_at')->get();

        return $themes->wrap(view('public-information::notices', ['notices' => $notices]));
    }
}
