<?php


namespace App\Services;

use App\Models\Warehouse;


class WarehouseService
{
    protected $warehouse;
    public function __construct(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;
    }
    public function all()
    {
        return $this->warehouse;
    }

    public function create($data)
    {
        $warehouse = $this->warehouse->create($data);
        return $warehouse;
    }

    public function update($data, $id)
    {
        $warehouse = $this->warehouse->find($id);
        $warehouse->update($data);
        return $warehouse;
    }

    public function delete($id)
    {
        return Warehouse::where('id', $id)->delete();
    }

    public function find($id)
    {
        return Warehouse::find($id);
    }
}
