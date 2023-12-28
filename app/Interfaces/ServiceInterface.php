<?php

namespace App\Interfaces;

interface ServiceInterface
{
    public function all(array $request = null);
    public function detail($id);
    public function create(array $data);
    public function update(array $data, $id);
    public function destroy($id);
}
