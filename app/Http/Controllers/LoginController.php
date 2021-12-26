<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\LoginReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class LoginController extends Controller
{
    private function loginUser(Request $request, $user)
    {
        $request->session()->regenerate();
        Auth::loginUsingId($user->id);
        // Create Login Report.
        $report = new LoginReport([
            'user_id' => $user->id,
            'username' => $user->username,
            'user_agent' => substr($request->userAgent(), 0, 190),
            'ip' => $request->ip(),
            'did' => $request->cookie('did'),
        ]);
        $report->save();
        $request->session()->put('login_id', $report->id);
        Device::updateOrCreate(['did' => $report->did], [
            'last_seen' => Carbon::now(),
            'username' => $report->username,
            'ip' => $report->ip,
            'user_agent' => $report->user_agent,
        ]);
    }

    public function doLogin(Request $request)
    {
        $request->validate(['captcha' => 'required|captcha'], ['captcha.captcha' => 'Captcha validation failed']);

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $user = User::query()->where('username', $credentials['username'])->where('password_plain', $credentials['password'])
            ->first();
        if (!$user) {
            return back()->with('danger', "Invalid credentials");
        }
        $this->loginUser($request, $user);
        return redirect()->intended()->with('success', "Welcome $user->username");
    }

    public function doLogout(Request $request)
    {
        if (Auth::check()) {
            if ($report = LoginReport::find($request->session()->get('login_id'))) {
                $report->logout_time = Carbon::now();
                $report->save();
            }
            Auth::logout();
            $request->session()->flush();
            $request->session()->regenerate();
            return redirect()->route('login')->with('success', "User logged out successfully");
        }
        return redirect()->route('login');
    }

    public function loginForm()
    {
        return Inertia::render('Login', ['captcha_src' => captcha_src('flat')]);
    }

    public function changePassForm()
    {
        $user = auth()->user();
        $info = ['username' => $user->username];
        return Inertia::render('Forms/ChangePassword', ['userInfo' => $info]);
    }

    public function changePass(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'password_old' => 'required',
            'password2' => 'required|same:password',
        ]);
        $user = auth()->user();
        if ($data['password_old'] != $user->password_plain) {
            return back()->withErrors(['password_old' => 'Invalid current password, try again']);
        }
        if ($request->input('logout_all')) {
            Auth::logoutOtherDevices($data['password_old']);
            Auth::logout();
        }
        User::query()->where('id', $user->id)->update([
            'username' => $data['username'],
            'password_plain' => $data['password'],
            'password' => Hash::make($data['password']),
        ]);
        return back()->with('success', 'Password was changed successfully');
    }

    public function report(Request $request)
    {
        $dpp = intval($request->session()->get('per_page'));
        if ($dpp <= 0) $dpp = 50;
        $q = DB::table('login_reports', 'lr')->leftJoin('devices', 'lr.did', '=', 'devices.did')->select('lr.id', 'lr.created_at', 'lr.username', 'lr.ip', 'lr.user_agent', 'logout_time', 'devices.trusted');

        $items = $q->orderBy('created_at', 'desc')->paginate($dpp);
        return Inertia::render('LoginReport', ['items' => $items, 'query' => $request->query()]);
    }

    public function takeAction(Request $request)
    {
        $data = $request->validate([
            'action' => 'required',
            'ids' => 'required|array',
        ]);
        $ids = [];
        foreach ($data['ids'] as $id) {
            $ids[] = intval($id);
        }
        $ids = "'" . implode("','", $ids) . "'";
        $count = 0;
        switch ($data['action']) {
            case 'delete':
                $count = LoginReport::whereIn('id', $data['ids'])->where('id', '<>', $request->session()->get('login_id'))->delete();
                break;
            case 'trust':
                $count = Device::whereRaw("did IN (SELECT DISTINCT did FROM login_reports WHERE id IN ($ids))")->update(['trusted' => true]);
                break;
            case 'distrust':
                $count = Device::whereRaw("did IN (SELECT DISTINCT did FROM login_reports WHERE id IN ($ids))")->update(['trusted' => false]);
                break;
            case 'clean':
                LoginReport::where('id', '<>', $request->session()->get('login_id'))->delete();
                return back()->with('info', "Login report cleaned up");
            default:
                return back()->with('danger', 'Unknown action specified');
        }
        return back()->with('info', "$count login(s) affected by the action \"$data[action]\"");
    }
}
