<?php

namespace Modules\Accounts\app\Services;


use Modules\Accounts\app\Models\Bank;

class BankService
{
    public function __construct(private Bank $bank)
    {
    }
    public function all()
    {
        return $this->bank;
    }

    public function find(int $id): Bank
    {
        return $this->bank->findOrFail($id);
    }
    public function create(array $data): Bank
    {
        return $this->bank->create($data);
    }

    public function update(Bank $bank, array $data): Bank
    {
        $bank->update($data);

        return $bank;
    }

    public function delete(Bank $bank): bool
    {
        return $bank->delete();
    }
}
