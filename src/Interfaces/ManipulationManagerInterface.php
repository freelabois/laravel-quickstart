<?php


namespace Freelabois\LaravelQuickstart\Interfaces;


interface ManipulationManagerInterface
{
    public function storeOrUpdate($values, int $id = null, array $relations = []);

    public function validate($values, int $id = null);

    public function persist($values, int $id = null, array $relations = []);

    public function destroy(int $id);
}
