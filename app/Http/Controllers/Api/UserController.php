<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $user_data = User::select('id', 'email', 'name')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->get()
            ->map(function ($user) {
                return [
                    'id' => (string) $user->id, // Cast id ke string
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'message' => 'Validation errors occured',
                'errors' => [
                    'name' => $errors->first('name'),
                    'email' => $errors->first('email'),
                    'password' => $errors->first('password')
                ]
            ], 422);
        }

        try {
            $user_data = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'User Created Successfully',
                'user' => $user_data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email,'.$id,
            'password' => 'string|min:8'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'message' => 'Validation errors occured',
                'errors' => [
                    'name' => $errors->first('name'),
                    'email' => $errors->first('email'),
                    'password' => $errors->first('password')
                ]
            ], 422);
        }

        try {
            $user_data = User::where('id', $id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'User Updated Successfully',
                'user' => $user_data
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            User::where('id', $id)->delete();
            return response()->json([
                'message' => 'User Deleted successfully'
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function datawithpagination(Request $request)
    {
        $limit = $request->get('limit', 10); // Default limit
        $page = $request->get('page', 1);   // Default page
        $search=$request->get('search');

        $usersData=User::select('id','email','name')
        ->where('name','like',"%{$search}%")
        ->orWhere('email','like',"%{$search}%")
        ->orderByDesc('created_at')
        ->paginate($limit);

        return response()->json($usersData);
    }

    public function searchUser(Request $request)
    {
        $search = $request->get('search');
        $users_data = User::select('id', 'email', 'name')
            ->where('name', 'like', "%" . $search . "%")
            ->orWhere('email', 'like', "%" . $search . "%")
            ->get();

        return response()->json($users_data);
    }
}
