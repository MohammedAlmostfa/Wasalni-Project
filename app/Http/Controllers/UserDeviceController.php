<?php
namespace App\Http\Controllers;

use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDeviceController extends Controller
{
    public function store(Request $request)
    {
        // تحقق من وجود التوكن و uid
        $request->validate([
            'fcm_token' => 'required|string',
            'uidd' => 'required|string',
        ]);

        $user = Auth::user();


        $existingDevice = $user->devices()->where('uidd', $request->uidd)->first();

        if ($existingDevice) {

            $existingDevice->update([
                'fcm_token' => $request->fcm_token,
                 'uidd' =>$existingDevice->uidd,
                 'user_id'=>$existingDevice->user_id,
            ]);

            return response()->json(['message' => 'Token updated successfully']);
        } else {

            $user->devices()->create([
                'fcm_token' => $request->fcm_token,
                'uidd' => $request->uidd,
            ]);

            return response()->json(['message' => 'Token stored successfully']);
        }
    }

}
