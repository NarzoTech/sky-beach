<?php

namespace Modules\Customer\app\Http\Services;

use Modules\Customer\app\Models\Area;

class AreaService
{
    protected $area;

    public function __construct(Area $area)
    {
        $this->area = $area;
    }

    public function getArea()
    {

        return $this->area;
    }

    public function saveArea($data): void
    {

        $this->area->create($data);
    }

    public function updateArea($data, $id): void
    {
        $this->area->find($id)->update($data);
    }

    public function deleteArea($id)
    {
        return $this->area->destroy($id);
    }
}
