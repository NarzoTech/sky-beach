<?php

namespace Modules\Service\app\Services;

use Modules\Service\app\Models\ServiceCategory;

class ServiceCategoryService
{
    protected $serviceCategoryRepository;

    public function __construct(private ServiceCategory $serviceCategory)
    {
        $this->serviceCategory = $serviceCategory;
    }

    public function all()
    {
        $service = $this->serviceCategory;

        if (request()->keyword) {
            $service = $service->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request()->keyword . '%');
            });
        }
        $sort = request('order_by') ? request('order_by') : 'asc';
        $service = $service->orderBy('name', $sort);
        return $service;
    }
    public function store(array $data)
    {
        return $this->serviceCategory->create($data);
    }

    public function update(int $id, array $data)
    {
        $serviceCategory = $this->serviceCategory->find($id);
        $serviceCategory->update($data);
    }

    public function delete(int $id)
    {
        $serviceCategory = $this->serviceCategory->find($id);
        $serviceCategory->delete();
    }
}
