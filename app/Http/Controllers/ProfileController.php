<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    // Traits
    use FileUploadTrait;

    // 인증 체크
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 보기
    public function index()
    {
        $user = User::findOrFail(request()->user()->user_id);
        $articles = $user->articles()->paginate(5);
        $user->setRelation('articles', $articles);

        return view('profile.index', compact('user'));
    }

    // 수정 폼
    public function edit()
    {
        $user = User::findOrFail(request()->user()->user_id);

        return view('profile.edit', compact('user'));
    }

    // 업데이트
    public function update(UpdateProfileRequest $request)
    {
        $user = User::findOrFail($request->user()->user_id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->has('image')) {
            $uploaded_file = $request->file('image');
            $stored_file_path = $this->uploadFile($uploaded_file, config('CONST.UPLOAD_PATH_PROFILE'), config('CONST.DISK'), $user->name);
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_PROFILE') . '/' . $user->image;

            if (Storage::exists($current_file_path)) {
                Storage::delete($current_file_path);
            }

            $user->image_name = $uploaded_file->getClientOriginalName();
            $user->image = basename($stored_file_path);
        }

        $user->save();
        Alert::success(null, __('msg/users.profile_changed'));

        return redirect()->route('profile.index');
    }

    // 비밀번호 변경 폼
    public function changePassword()
    {
        return view('profile.password.change');
    }

    // 비밀번호 업데이트
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = User::findOrFail($request->user()->user_id);
        $user->password = bcrypt($request->new_password);
        $user->save();
        Alert::success(null, __('msg/users.password_changed'));

        return redirect()->route('profile.index');
    }
}
