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
        // Lấy danh sách blog mới nhất
        return response()->json(Blog::orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string',
            'noi_dung' => 'required',
            'hinh_anh' => 'nullable|image|max:2048' // Validate ảnh
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->tieu_de); // Tạo slug SEO
        $data['user_id'] = auth()->id() ?? 1; // Tạm thời lấy ID 1 nếu chưa login

        // Xử lý upload ảnh
        if ($request->hasFile('hinh_anh')) {
            $file = $request->file('hinh_anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/blogs'), $filename);
            $data['hinh_anh'] = 'uploads/blogs/' . $filename;
        }

        $blog = Blog::create($data);
        return response()->json(['success' => true, 'data' => $blog]);
    }

    public function show($id)
    {
        return response()->json(Blog::find($id));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) return response()->json(['success' => false, 'message' => 'Không tìm thấy blog'], 404);

        $data = $request->all();
        if ($request->has('tieu_de')) {
            $data['slug'] = Str::slug($request->tieu_de);
        }

        // Xử lý ảnh mới nếu có
        if ($request->hasFile('hinh_anh')) {
            // Xóa ảnh cũ nếu cần (tùy chọn)
            $file = $request->file('hinh_anh');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/blogs'), $filename);
            $data['hinh_anh'] = 'uploads/blogs/' . $filename;
        }

        $blog->update($data);
        return response()->json(['success' => true, 'data' => $blog]);
    }

    public function destroy($id)
    {
        Blog::destroy($id);
        return response()->json(['success' => true]);
    }
}