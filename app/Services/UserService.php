<?php

namespace App\Services;

use App\Enums\TypeActivityEnum;
use App\Events\ActivityCreated;
use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    public function getUsers()
    {
        $users = User::query()
            ->when(request('search'), function ($query) {
                $query->whereAny(['fullname', 'ci'], 'like', '%' . request('search') . '%');
            })
            ->when(request('fullname'), function ($query) {
                $query->where('fullname', 'like', '%' . request('fullname') . '%');
            })
            ->when(request('ci'), function ($query) {
                $query->where('ci', 'like', '%' . request('ci') . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return $users;
    }


    public function store($data)
    {

        try {

            return DB::transaction(function () use ($data) {

                if (request()->hasFile('photo')) {

                    $extension = request()->file('photo')->getClientOriginalExtension();

                    $fileName = Str::slug($data['ci']) . '_' . time() . '.' . $extension;

                    $data['photo'] = request()->file('photo')->storeAs(
                        'users',
                        $fileName,
                        'public'
                    );
                }

                $data['password'] = Hash::make($data['ci']);
                $user = User::create($data);

                /** @var User $accountableUser */
                $accountableUser = Auth::user();


                $url = route('users.show', ['user' => $user->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::CREATE_USER->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - UserService - Creando usuario: ' . $e->getMessage(), [$data]);

            throw new Exception('Hubo un problema creando el usuario');
        }
    }

    public function update($data, $user)
    {

        try {

            return DB::transaction(function () use ($data, $user) {

                if (request()->hasFile('photo')) {

                    if ($user->photo)
                        Storage::disk('public')->delete($user->photo);

                    $extension = request()->file('photo')->getClientOriginalExtension();

                    $fileName = Str::slug($user->ci) . '_' . time() . '.' . $extension;

                    $data['photo'] = request()->file('photo')->storeAs(
                        'users',
                        $fileName,
                        'public'
                    );
                }

                if (request()->filled('new_password')) {
                    $data['password'] = Hash::make($data['new_password']);
                }

                $user->update($data);


                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = route('users.show', ['user' => $user->id]);
                event(new ActivityCreated($accountableUser, TypeActivityEnum::UPDATE_USER->value, $url));

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - UserService - Actualizando usuario: ' . $e->getMessage(), [$data]);

            throw new Exception('Hubo un problema actualizando el usuario');
        }
    }

    public function destroy($user)
    {
        try {

            return DB::transaction(function () use ($user) {

                /** @var User $accountableUser */
                $accountableUser = Auth::user();

                $url = '#';
                event(new ActivityCreated($accountableUser, TypeActivityEnum::DELETE_USER->value, $url));


                if (User::count() == 1)
                    throw new Exception("No puede eliminar el único usuario que queda del sistema", 400);

                if ($user->photo)
                    Storage::disk('public')->delete($user->photo);

                $user->delete();
                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - UserService - Eliminando usuario: ' . $e->getMessage(), [$user]);

            throw $e;
        }
    }



    // public function deleteUser($usuario)
    // {
    //     $authUserId = auth()->id();
    //     $usuario->id == $authUserId ? throw new Exception("No puedes eliminar tu propio usuario", 401) : null;

    //     $usersDeleted = User::where('status',0)->count();
    //     $number = $usersDeleted + 1;



    //     $fields = $usuario->getAttributes();
    //     unset(
    //         $fields['id'],
    //         $fields['status'],
    //         $fields['search'],
    //         $fields['specialty_id'],
    //         $fields['name'],
    //         $fields['last_name'],
    //         );

    //     $usuario->update(array_map(function ($value) use ($number){

    //         return $value .'deleted-'.$number;

    //     }, $fields));

    //     $usuario->update(['status' => 0]);

    //     $this->changePhotoName($usuario,$number);

    //     return 0;
    // }

    // public function changePassword($data){
    //     $user = auth()->user();

    //     if (!Hash::check($data['currentPassword'], $user->password))
    //         throw new Exception("La contraseña actual es incorrecta", 403);

    //     if ($data['newPassword'] != $data['confirmPassword'])
    //         throw new Exception("La nueva contraseña no coincide con la confirmación", 403);

    //     $user->password = Hash::make($data['newPassword']);
    //     $user->save();
    //     $userID = $user->id;

    //     Auth::logout();

    //     DB::table('sessions')
    //             ->where('user_id', $userID)
    //             ->delete();

    //     return 0;

    // }


    public function updateProfile($data)
    {

        try {

            return DB::transaction(function () use ($data) {

                /** @var User $user */
                $user = Auth::user();

                if (request()->hasFile('photo')) {

                    if ($user->photo)
                        Storage::disk('public')->delete($user->photo);

                    $extension = request()->file('photo')->getClientOriginalExtension();

                    $fileName = Str::slug($user->ci) . '_' . time() . '.' . $extension;

                    $data['photo'] = request()->file('photo')->storeAs(
                        'users',
                        $fileName,
                        'public'
                    );
                }

                if (request()->filled('new_password')) {
                    $data['password'] = Hash::make($data['new_password']);
                }

                $user->update($data);

                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - UserService - Actualizando Perfil: ' . $e->getMessage(), [$data]);

            throw new Exception('Hubo un problema actualizando el perfil');
        }
    }

    public function destroyProfile()
    {
        try {

            $user = Auth::user();


            return DB::transaction(function () use ($user) {

                /** @var User $user */



                if (User::count() == 1)
                    throw new Exception("No puede eliminar el único usuario que queda del sistema", 400);

                if ($user->photo)
                    Storage::disk('public')->delete($user->photo);


                $user->delete();


                return 0;
            });
        } catch (Exception $e) {

            Log::info('Error - UserService - Eliminando Perfil: ' . $e->getMessage(), [$user]);

            throw $e;
        }
    }


    // private function handlePhoto($data){

    //     if (isset($data['photo']) && $data['photo']->isValid()) {
    //         $fileName = $data['ci'] . '-profile.webp';
    //         $image = Image::make($data['photo']);

    //         $image->resize(180, null, function ($constraint) {
    //             $constraint->aspectRatio();
    //             $constraint->upsize();
    //         });

    //         $image->save(storage_path('app/public/users/' . $fileName), 100); // 100 es el nivel de calidad (0-100)

    //         return $fileName;
    //     }

    //     throw new Exception("La imagen no es valida, intente con otra", 500);

    //     }
    // public function handleUpdatePhoto($data, $user){

    //         $fileName = $user->id . '-profile_picture.webp';
    //         $filePath = storage_path('app/public/users/' . $fileName);


    //         // Verifica si la imagen existe y la elimina
    //         if (file_exists($filePath)) {
    //             unlink($filePath); // Elimina el archivo existente
    //         }


    //         // Crea la nueva imagen
    //         $image = Image::make($data['photo']);

    //         $image->resize(180, null, function ($constraint) {
    //             $constraint->aspectRatio();

    //             $constraint->upsize();
    //         });


    //         $image->save($filePath, 100);

    //         return $fileName;

    // }






}
