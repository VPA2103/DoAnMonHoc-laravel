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
        return response()->json(
            Blog::where('trang_thai', 1)
                ->orderByDesc('created_at')
                ->get()
        );
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }

        $validated = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'trang_thai' => 'nullable|in:0,1', // Thêm validate trạng thái (0 = Draft, 1 = Published)
        ]);

        // ✅ TẠO SLUG KHÔNG TRÙNG
        $baseSlug = Str::slug($request->tieu_de);
        $slug = $baseSlug;
        $count = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }
        // ✅ UPLOAD ẢNH (QUAN TRỌNG)
        $path = null;

        $data = $validated;
        $data['slug'] = $slug;
        $data['ma_nguoi_dung'] = auth()->id();
        $data['trang_thai'] = $request->trang_thai ?? 1; // Mặc định là Published (1) khi tạo mới
            //  XỬ LÝ LƯU ẢNH 
        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('blogs', 'public');
        }

        $blog = Blog::create([
            'tieu_de' => $request->tieu_de,
            'slug' => $slug,
            'noi_dung' => $request->noi_dung,
            'hinh_anh' => $path, // ✅ LƯU ĐƯỜNG DẪN FILE
            'ma_nguoi_dung' => auth()->id(),
            'trang_thai' => 0 // CHỜ DUYỆT
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đăng blog thành công, chờ admin duyệt',
            'data' => $blog
        ]);
    }

    public function show($id)
    {
    $blog = Blog::with('nguoiDung')->findOrFail($id);

    $blog->hinh_anh_url = $blog->hinh_anh
        ? asset('storage/' . $blog->hinh_anh)
        : null;

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
            'trang_thai' => 'nullable|in:0,1', // Thêm validate trạng thái khi update
        ]);

        $data = [
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'trang_thai' => 0 // SỬA → QUAY LẠI CHỜ DUYỆT
        ];

        // ✅ SLUG KHÔNG TRÙNG (TRỪ CHÍNH NÓ)
        $baseSlug = Str::slug($request->tieu_de);
        $slug = $baseSlug;
        $count = 1;

        while (
            Blog::where('slug', $slug)
                ->where('ma_blog', '!=', $id)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $data['slug'] = $slug;

        // ✅ UPDATE ẢNH
        if ($request->hasFile('hinh_anh')) {
            if ($blog->hinh_anh && Storage::disk('public')->exists($blog->hinh_anh)) {
                Storage::disk('public')->delete($blog->hinh_anh);
            }

            $data['hinh_anh'] = $request->file('hinh_anh')->store('blogs', 'public');
        }

        // Cập nhật trạng thái nếu có gửi
        if ($request->has('trang_thai')) {
            $data['trang_thai'] = $request->trang_thai;
        }
        // Nếu không gửi ảnh mới, Laravel sẽ tự giữ nguyên ảnh cũ vì $data chỉ chứa field được validate
        $blog->update($data);

        return response()->json([
            'success' => true,
            'data' => $blog
        ]);
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

        if ($blog->hinh_anh && Storage::disk('public')->exists($blog->hinh_anh)) {
            Storage::disk('public')->delete($blog->hinh_anh);
        }

        $blog->delete();

        return response()->json(['success' => true]);
    }
    // ADMIN
    public function duyetBlog(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:1,2'

    public function updateTrangThai(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:0,1'
        ]);

        $blog = Blog::findOrFail($id);


        $blog->update([
            'trang_thai' => $request->trang_thai
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->trang_thai == 1
                ? 'Duyệt blog thành công'
                : 'Từ chối blog thành công',
            'data' => $blog
        ]);
    }

    public function blogChoDuyet()
    {
        return response()->json(
            Blog::where('trang_thai', 0)
                ->orderByDesc('created_at')
                ->get()
        );
    }

    public function indexAdmin()
    {
        return response()->json([
            'data' => Blog::with('nguoiDung')
                ->orderByDesc('created_at')
                ->get()
        ]);
    }

   public function blogCuaToi()
    {
    $blogs = Blog::where('ma_nguoi_dung', auth()->id())
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($blog) {
            $blog->hinh_anh_url = $blog->hinh_anh
                ? asset('storage/' . $blog->hinh_anh)
                : null;
            return $blog;
        });

    return response()->json($blogs);
    }

}
        // Kiểm tra quyền (chỉ owner mới được đổi trạng thái)
        if ($blog->ma_nguoi_dung !== auth()->id()) {
            return response()->json(['message' => 'Không có quyền'], 403);
        }

        $blog->update(['trang_thai' => $request->trang_thai]);
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $blog
        ]);
    }
}
