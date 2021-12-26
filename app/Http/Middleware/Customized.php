<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class Customized
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $did = $request->cookie('did');
        if (empty($did)) {
            $did = $this->generateDeviceToken();
            Cookie::queue("did", $did, 525600 * 20);
        }
        if (($dpp = $request->query('per_page')) > 0) {
            $request->session()->put('per_page', $dpp);
        }
        Device::where('did', $did)->update([
            'last_seen' => Carbon::now(),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 190),
        ]);
        return $next($request);
    }

    private function generateDeviceToken(): string
    {
        return bin2hex(random_bytes(24));
    }
}
