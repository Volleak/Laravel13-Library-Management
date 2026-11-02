<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibrarySettingController extends Controller
{
    // Admin: View current library settings
    public function index()
    {
        // Assuming settings are stored in a simple key-value table
        $settings = DB::table('library_settings')->pluck('setting_value', 'setting_key');

        return view('settings.index', compact('settings'));

        
    }

    // Admin: Update library settings
    public function update(Request $request)
    {
        foreach ($request->except(['_token', '_method']) as $key => $value) {

            // Loop through the submitted settings and update the database
            foreach ($request->except(['_token']) as $key => $value) {
                DB::table('library_settings')
                    ->updateOrInsert(
                        ['setting_key' => $key],
                        ['setting_value' => $value, 'updated_at' => now()]
                    );
            }

            return back()->with('success', 'Library settings updated successfully.');
        }
    }
}