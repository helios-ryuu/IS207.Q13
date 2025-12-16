<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    protected ImageUploadService $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

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
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
            ]);

            $user = $request->user();

            if ($request->file('avatar')) {
                // Xóa ảnh cũ nếu có
                if ($user->avatar) {
                    $this->imageService->delete($user->avatar_url);
                }

                // Upload ảnh mới
                $avatarUrl = $this->imageService->upload($request->file('avatar'), 'avatars/users');

                // Lưu đường dẫn vào DB (chỉ lưu relative path)
                $user->update(['avatar' => $this->extractPath($avatarUrl)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'avatar_url' => $user->avatar_url
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('[AVATAR] Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    // 5. Đổi Cover
    public function changeCover(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // Max 10MB
            ]);

            $user = $request->user();

            if ($request->file('cover')) {
                // Xóa ảnh cũ nếu có
                if ($user->cover) {
                    $this->imageService->delete($user->cover_url);
                }

                // Upload ảnh mới
                $coverUrl = $this->imageService->upload($request->file('cover'), 'covers/users');

                // Lưu đường dẫn vào DB (chỉ lưu relative path)
                $user->update(['cover' => $this->extractPath($coverUrl)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cover updated successfully',
                'cover_url' => $user->cover_url
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload cover: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extract relative path from full URL
     * Delegates to ImageUploadService for consistent path handling
     */
    private function extractPath(string $url): string
    {
        return $this->imageService->extractPath($url);
    }
}