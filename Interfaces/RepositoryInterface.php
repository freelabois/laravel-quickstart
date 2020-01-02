<?php


namespace Freelabois\LaravelQuickstart\Interfaces;

interface RepositoryInterface
{

    public function find(int $id, array $with = []);

    public function list(array $filters = [], array $with = [], $pagination = 45);

    public function applyFilters(array $filters = []);

    public function injectFiltersOnQuery();

    public function setPresenter($resource = null);

    public function present();

    public function newQuery();

    public function storeOrUpdate($values, int $id = null, array $relations = []);

    public function persist(array $relations = []);

    public function persistPolymorphic(array $relations);

    public function destroy(int $id);

    public function associate(array $relations);

}
