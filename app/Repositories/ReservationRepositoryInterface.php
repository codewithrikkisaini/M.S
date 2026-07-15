<?php

namespace App\Repositories;

interface ReservationRepositoryInterface
{
    public function getAllPaginated($search, $perPage = 10);
    public function findById($id);
    public function createOrUpdate($id, array $data);
    public function delete($id);
    public function getEventsByDateRange($start, $end);
}
