<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $role = Auth::user()->role ?? 'customer';

            if (in_array($role, ['admin', 'superadmin', 'bendahara', 'medical_record', 'kurir'])) {
                return redirect()->route('dashboard.admin');
            } elseif ($role === 'ahli_gizi') {
                return redirect()->route('ahli_gizi.orders');
            }

            return redirect()->route('dashboard.customer');
        }

        // belum login → tampilkan landing page
        return view('welcome');
    }
}
