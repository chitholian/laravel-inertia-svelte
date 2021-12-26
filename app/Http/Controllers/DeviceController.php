<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceController extends Controller
{
    public function report(Request $request)
    {
        $dpp = intval($request->session()->get('per_page'));
        if ($dpp <= 0) $dpp = 50;
        $q = Device::query()->selectRaw("id, username, ip, last_seen, last_seen AS last_active, user_agent, trusted, IF(did = ?,1,0) as this_device", [$request->cookie('did')]);

        $items = $q->orderBy('last_active', 'desc')->paginate($dpp);
        return Inertia::render('Devices', ['items' => $items, 'query' => $request->query()]);
    }

    public function takeAction(Request $request)
    {
        $data = $request->validate([
            'action' => 'required',
            'ids' => 'required|array',
        ]);
        $count = 0;
        $q = Device::whereIn('id', $data['ids']);
        switch ($data['action']) {
            case 'delete':
                $count = $q->where('did', '<>', $request->cookie('did'))->delete();
                break;
            case 'trust':
                $count = $q->update(['trusted' => true]);
                break;
            case 'distrust':
                $count = $q->update(['trusted' => false]);
                break;
            case 'clean':
                Device::where('did', '<>', $request->cookie('did'))->delete();
                return back()->with('info', "Device list cleaned up");
            default:
                return back()->with('danger', 'Unknown action specified');
        }
        return back()->with('info', "$count devices affected by the action \"$data[action]\"");
    }
}
