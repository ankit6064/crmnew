<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\RestrictEmployeelogin;

class RestrictSpecificEmailByHost
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply on login POST requests
        if ($request->is('login') && $request->isMethod('post')) {
            $blockedHost = '127.0.0.1'; // the disallowed IP/domain
            $checkblock = RestrictEmployeelogin::where('employee_email',$request->input('email'))->first();

            $currentHost = $request->getHost();
            // dd($currentHost);
            $loginEmail = $request->input('email');

            if ($currentHost === $blockedHost && !empty($checkblock)) {
                return back()->withErrors(['email' => 'You dont have permission to access.Please contact your Manager.‹‹']);
            }
        }

        return $next($request);
    }
}
