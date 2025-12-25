<?php

namespace Modules\Service\app\Services;

use Illuminate\Http\Request;
use Modules\Service\app\Models\Service;


class ServicesService
{
    protected $service;

    public function __construct(Service $service, private ServiceCategoryService $category)
    {
        $this->service = $service;
    }

    public function all()
    {
        $service = $this->service;

        if (request()->keyword) {
            $service = $service->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request()->keyword . '%');
            });
        }

        $sort = request('order_by') ? request('order_by') : 'asc';
        $service = $service->orderBy('name', $sort);
        return $service;
    }

    public function find($id)
    {
        return $this->service->find($id);
    }

    public function store(Request $request): void
    {
        $filename = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = file_upload($image);
        }

        $this->service->create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $filename,
            'price' => $request->price,
            'status' => $request->status,
            'category_id' => $request->category_id
        ]);
    }

    public function update(int $id, Request $request)
    {
        $service = $this->service->find($id);
        $filename = $service->image;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = file_upload($image, oldFile: $service->image);
        }
        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $filename,
            'price' => $request->price,
            'status' => $request->status,
            'category_id' => $request->category_id
        ]);
    }

    public function destroy(int $id)
    {
        $service = $this->service->find($id);
        if ($service->image) {
            delete_file($service->image);
        }
        $service->delete();
    }

    public function getCategories()
    {
        return $this->category->all()->where('status', 1)->get();
    }

    public function addToWishlist(string $type, $id)
    {
        $service = $this->service->find($id);

        if ($type == 'add') {
            $service->is_favourite = 1;
        } else {
            $service->is_favourite = 0;
        }
        $service->save();
    }
}
