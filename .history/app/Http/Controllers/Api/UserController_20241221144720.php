<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_data = User::select('id', 'email', 'name')->get()->map(function ($user) {
            return [
                'id' => (string) $user->id, // Cast id to string
                'email' => $user->email,
                'name' => $user->name,
            ];
        });

        return response()->json($user_data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_data=$request->validate([
            'name'=>'required|string',
            'email'=>'required|email',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function datawithpagination(Request $request)
    {
        $limit = $request->get('limit', 10); // Default limit
        $page = $request->get('page', 1);   // Default page

        $users_data = User::select('id', 'email', 'name')
            ->paginate($limit, ['*'], 'page', $page);

        // Cast 'id' to string for each user
        $users_data->getCollection()->transform(function ($user) {
            $user->id = (string) $user->id;
            return $user;
        });

        // Return only the 'data' array
        return response()->json($users_data->items());

    }

    public function searchUser(Request $request){
        $search=$request->get('search');
        $users_data = User::select('id', 'email', 'name')
        ->where('name','like',"%".$search."%")
        ->orWhere('email','like',"%".$search."%")
        ->get();

        return response()->json($users_data);
    }
}
