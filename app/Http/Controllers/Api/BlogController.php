<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        return response()->json(Blog::orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Bạn chưa đăng nhập!'], 401);
        }

        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
        ]);

        // Tạo slug cơ bản
        $baseSlug = Str::slug($request->tieu_de);
        $slug = $baseSlug;
        $count = 1;

        // Nếu trùng thì thêm -1, -2,...
        while (Blog::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $data = $validated;
        $data['slug'] = $slug;
        $data['ma_nguoi_dung'] = auth()->id();

        $blog = Blog::create($data);

        return response()->json(['success' => true, 'data' => $blog], 201);
    }

    public function show($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Không tìm thấy blog'], 404);
        }
        return response()->json($blog);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Không tìm thấy blog'], 404);
        }

        if ($blog->ma_nguoi_dung !== auth()->id()) {
            return response()->json(['message' => 'Không có quyền sửa'], 403);
        }

        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
        ]);

        $data = $validated;

        if ($request->filled('tieu_de')) {
            $baseSlug = Str::slug($request->tieu_de);
            $slug = $baseSlug;
            $count = 1;

            // Tránh trùng với các bài khác (không tính chính nó)
            while (Blog::where('slug', $slug)->where('ma_blog', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $data['slug'] = $slug;
        }

        $blog->update($data);

        return response()->json(['success' => true, 'data' => $blog]);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Không tìm thấy blog'], 404);
        }

        if ($blog->ma_nguoi_dung !== auth()->id()) {
            return response()->json(['message' => 'Không có quyền xóa'], 403);
        }

        $blog->delete();

        return response()->json(['success' => true]);
    }
}