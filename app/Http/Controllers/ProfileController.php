<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePassword;
use App\Http\Requests\UpdateProfile;
use App\Models\User;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Traits
    use FileUpload;
    const storage_disk = 'public';

    // 인증 체크
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 보기
    public function index()
    {
        $user = User::findOrFail(request()->user()->id);
        $articles = $user->articles()->paginate(5);
        $user->setRelation('articles', $articles);

        return view('profile.index', compact('user'));
    }

    // 수정 폼
    public function edit()
    {
        $user = User::findOrFail(request()->user()->id);

        return view('profile.edit', compact('user'));
    }

    // 업데이트
    public function update(UpdateProfile $request)
    {
        $user = User::findOrFail($request->user()->id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->has('image')) {
            $uploaded_file = $request->file('image');
            $stored_file_path = $this->uploadFile($uploaded_file, config('CONST.UPLOAD_PATH_PROFILE'), config('CONST.DISK'), $user->name);
            $disk = Storage::disk(config('CONST.DISK'));
            $current_file_path = '/' . config('CONST.UPLOAD_PATH_PROFILE') . '/' . $user->image;

            if ($disk->exists($current_file_path)) {
                $disk->delete($current_file_path);
            }

            $user->image_name = $uploaded_file->getClientOriginalName();
            $user->image = basename($stored_file_path);
        }

        $user->save();

        return redirect()->route('profile.index');
    }

    // 비밀번호 변경 폼
    public function changePassword()
    {
        return view('profile.password.change');
    }

    // 비밀번호 업데이트
    public function updatePassword(UpdatePassword $request)
    {
        $user = User::findOrFail($request->user()->id);
        $user->password = bcrypt($request->new_password);
        $user->save();

        return redirect()->route('profile.index')->with('success', __('ui/users.password_changed'));
    }
}
