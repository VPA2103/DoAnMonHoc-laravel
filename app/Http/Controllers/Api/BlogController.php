<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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
            //  XỬ LÝ LƯU ẢNH 
        if ($request->hasFile('hinh_anh')) {
            // Lưu vào storage/app/public/blogs
            // Hàm store sẽ trả về đường dẫn ví dụ: "blogs/abcxyz.jpg"
            $path = $request->file('hinh_anh')->store('blogs', 'public');
            $data['hinh_anh'] = $path;
        }
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
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
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
        // XỬ LÝ CẬP NHẬT ẢNH (QUAN TRỌNG)
        if ($request->hasFile('hinh_anh')) {
            // Bước A: Xóa ảnh cũ đi cho đỡ rác (nếu có)
            if ($blog->hinh_anh && Storage::disk('public')->exists($blog->hinh_anh)) {
                Storage::disk('public')->delete($blog->hinh_anh);
            }

            // Lưu ảnh mới
            $path = $request->file('hinh_anh')->store('blogs', 'public');
            $data['hinh_anh'] = $path;
        }

        // Nếu không gửi ảnh mới, Laravel sẽ tự giữ nguyên ảnh cũ vì $data chỉ chứa field được validate
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

        // Xóa luôn ảnh trong storage khi xóa bài viết
        if ($blog->hinh_anh && Storage::disk('public')->exists($blog->hinh_anh)) {
            Storage::disk('public')->delete($blog->hinh_anh);
        }
        $blog->delete();

        return response()->json(['success' => true]);
    }
}