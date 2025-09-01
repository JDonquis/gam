<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUsersRequest;
use Exception;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{

    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = $this->userService->getUsers();
        return view('dashboard.users')->with(compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.crud_users.create_user');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        try {

            $this->userService->store($request->validated());

            return redirect()->route('users.index')->with('success','Usuario creado exitosamente');

        } catch (Exception $e) {

            return back()
            ->withInput($request->only(['fullname','ci','phone_number']))
            ->withErrors([
                'status' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $activities = Activity::where('user_id',$user->id)
        ->with('typeActivity')
        ->orderBy('id','desc')
        ->limit(7)
        ->get();

        return view('dashboard.crud_users.show_user')->with(compact('user','activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('dashboard.crud_users.edit_user')->with(compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {

            $this->userService->update($request->validated(), $user);

            return redirect()->route('users.show',['user' => $user->id])->with('success','Usuario actualizado exitosamente');

        } catch (Exception $e) {

            return back()
            ->withInput($request->only(['fullname','ci','phone_number']))
            ->withErrors([
                'status' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {

            $this->userService->destroy($user);

            return redirect()->intended()->route('users.index')->with('success','Usuario eliminado con exito');

        } catch (Exception $e) {

            return back()
            ->with('error', $e->getMessage())
            ->withErrors([
                'status' => $e->getMessage()
            ]);
        }
    }

    public function profile(){

        $user = Auth::user();
        $activities = Activity::where('user_id',$user->id)
        ->with('typeActivity')
        ->orderBy('id','desc')
        ->limit(7)
        ->get();

        return view('dashboard.profile')->with(compact('activities'));
    }

    public function editProfile(){

        $data = Auth::user();
        return view('dashboard.crud_profile.edit_profile')->with(compact('data'));
    }

    public function updateProfile(UpdateProfileRequest $request){

        try {

            $this->userService->updateProfile($request->validated());

            return redirect()->route('profile')->with('success','Perfil actualizado exitosamente');

        } catch (Exception $e) {

            return back()
            ->withInput($request->only(['fullname','ci','phone_number']))
            ->withErrors([
                'status' => $e->getMessage()
            ]);
        }

    }

    public function destroyProfile(){

        try {

            $this->userService->destroyProfile();

            return redirect()->route('login')->with('success','Usuario eliminado con exito');

        } catch (Exception $e) {

            return back()
            ->with('error', $e->getMessage())
            ->withErrors([
                'status' => $e->getMessage()
            ]);
        }

    }
}
