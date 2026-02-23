<?php

namespace Infrastructure\Controllers;

use Infrastructure\Persistence\SpecialtyRepository;

class SpecialtyController
{
    public function getAll(): void
    {
        try {
            $repo = new SpecialtyRepository();
            $specialties = $repo->findActive();

            $result = [];
            foreach ($specialties as $specialty) {
                $result[] = $specialty->toArray();
            }

            response(['success' => true, 'data' => $result], 200);

        } catch (\Exception $e) {
            response(['error' => 'Error al obtener especialidades'], 500);
        }
    }
}
