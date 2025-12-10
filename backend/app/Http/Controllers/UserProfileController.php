<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    // 1. Xem Profile
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    // 2. Cập nhật Profile
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    // 3. Đổi mật khẩu
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        // Kiểm tra mật khẩu cũ
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 400);
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }

    // 4. Đổi Avatar
    public function changeAvatar(Request $request): JsonResponse
    {
        try {
            \Log::info('[AVATAR] Upload started', [
                'user_id' => $request->user()->id,
                'disk' => config('filesystems.default'),
                'gcs_config' => [
                    'project_id' => config('filesystems.disks.gcs.project_id'),
                    'bucket' => config('filesystems.disks.gcs.bucket'),
                ]
            ]);

            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            ]);

            $user = $request->user();
            $disk = config('filesystems.default');

            \Log::info('[AVATAR] Validation passed', [
                'disk' => $disk,
                'has_file' => $request->hasFile('avatar'),
            ]);

            if ($request->file('avatar')) {
                // Xóa ảnh cũ nếu có
                if ($user->avatar) {
                    try {
                        \Log::info('[AVATAR] Deleting old avatar', ['path' => $user->avatar]);
                        Storage::disk($disk)->delete($user->avatar);
                    } catch (\Exception $e) {
                        \Log::warning('[AVATAR] Failed to delete old avatar', [
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Generate unique filename: avatars/users/user-{id}-{hash}.jpg
                $extension = $request->file('avatar')->getClientOriginalExtension();
                $filename = 'user-' . $user->id . '-' . uniqid() . '.' . $extension;
                
                \Log::info('[AVATAR] Storing file', [
                    'filename' => $filename,
                    'disk' => $disk,
                    'size' => $request->file('avatar')->getSize()
                ]);

                $path = $request->file('avatar')->storeAs('avatars/users', $filename, $disk);
                
                \Log::info('[AVATAR] File stored', ['path' => $path]);
                
                // Lưu đường dẫn vào DB
                $user->update(['avatar' => $path]);
            }

            // Tạo public URL dựa vào disk
            if ($disk === 'gcs') {
                $bucket = config('filesystems.disks.gcs.bucket');
                $avatarUrl = "https://storage.googleapis.com/{$bucket}/{$user->avatar}";
            } else {
                $avatarUrl = asset('storage/' . $user->avatar);
            }

            \Log::info('[AVATAR] Upload completed', ['url' => $avatarUrl]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar_url' => $avatarUrl
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('[AVATAR] Validation error', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('[AVATAR] Upload failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar: ' . $e->getMessage()
            ], 500);
        }
    }
}