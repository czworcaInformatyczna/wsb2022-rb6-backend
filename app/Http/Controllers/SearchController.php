<?php

namespace App\Http\Controllers;

use App\Models\Licence;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'searched' => 'string|required',
            'per_page' => 'integer|min:1|max:25'
        ]);
        $user = Auth()->user();

        $response = collect();

        $assets = Asset::where('name', 'like', "%{$validated['searched']}%")->get();
        foreach ($assets as $key => $value) {
            $assets[$key]['model'] = 'asset';
        }

        $response = $response->merge($assets);
        $licences = Licence::where('name', 'like', "%{$validated['searched']}%")->get();
        foreach ($licences as $key => $value) {
            $licences[$key]['model'] = 'licence';
        }
        $response = $response->merge($licences);

        $users = User::where('name', 'like', "%{$validated['searched']}%")
            ->orWhere('surname', 'like', "%{$validated['searched']}%")
            ->get();
        foreach ($users as $key => $value) {
            $users[$key]['model'] = 'user';
        }
        $response = $response->merge($users);

        return $response->sortBy('name')->splice(0, 10);
    }
}
