<?php

namespace Modules\Customer\app\Http\Services;

use Modules\Customer\app\Models\UserGroup;

class UserGroupService
{
    protected $userGroup;

    public function __construct(UserGroup $userGroup)
    {
        $this->userGroup = $userGroup;
    }

    public function getUserGroup($type = 'list')
    {
        $user = $this->userGroup;
        if ($type == 'list') {
            if (request()->keyword) {
                $user =  $user->where(function ($q) {
                    $q->where('name', 'LIKE', '%' . request()->keyword . '%')
                        ->orWhere('description', 'LIKE', '%' . request()->keyword . '%')
                        ->orWhere('discount', 'LIKE', '%' . request()->keyword . '%');
                });
            }
        }

        $sort = request('order_by') ? request('order_by') : 'asc';
        $user = $user->orderBy('name', $sort);
        return $user;
    }

    public function store(array $data): void
    {
        $this->userGroup->create($data);
    }


    public function update(array $data, int $id): void
    {
        $this->userGroup->find($id)->update($data);
    }

    public function destroy(int $id)
    {
        return $this->userGroup->destroy($id);
    }
}
