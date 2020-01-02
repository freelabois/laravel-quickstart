<?php


namespace App\Extendables;

use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model = null;
    protected $searchableFields = [];
    /**
     * @var JsonResource
     */
    protected $presenter = null;
    protected $returnable = null;
    /**
     * @var Builder
     */

    protected $query = null;
    protected $saved = null;
    /**
     * @var array
     */
    protected $filters;
    protected $orderBy = null;
    protected $orderByType = null;
    private $polymorphic = false;

    /**
     * @param array $filters
     * @param array $with
     * @param int $pagination
     * @return AnonymousResourceCollection|null
     */
    public function list(array $filters = [], array $with = [], $pagination = 45)
    {
        $query = $this->newQuery();
        $query->with($with);
        if ($pagination === "false" || $pagination === false) {
            $pagination = 9223372036854775807;
        }
        if (!empty($filters)) {
            $this->applyFilters($filters);
            $this->injectFiltersOnQuery();
        }
        $this->order();
        $this->returnable = $query->paginate($pagination);
        return $this->present(true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return $this->query = $this->model::query();
    }

    /**
     * @param array $filters
     */
    public function applyFilters(array $filters = [])
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     *
     */
    public function injectFiltersOnQuery()
    {
        foreach ($this->searchableFields as $searchableField) {
            if (in_array($searchableField['field'], array_keys($this->filters))) {
                $this->query->where(
                    $searchableField['field'],
                    $searchableField['operator'] ?? "=",
                    isset($searchableField['operator']) && $searchableField['operator'] === 'ilike' ? "%" . $this->filters[$searchableField['field']] . "%" : $this->filters[$searchableField['field']]
                );
            }
        }
    }

    protected function order()
    {
        $order = $this->orderBy ?? (new $this->model)->orderBy;
        if (isset($order)) {
            $this->query->orderBy($order, $this->orderByType ?? (new $this->model)->orderByType ?? "asc");
        }
        return $this;
    }

    /**
     * @param bool $return_empty
     * @return AnonymousResourceCollection|null
     */
    public function present($return_empty = false)
    {
        if (!$this->returnable && !$return_empty) {
            throw new ModelNotFoundException();
        }

        if (!$this->presenter) {
            return $this->returnable;
        }

        if ($this->returnable instanceof LengthAwarePaginator) {
            return $this->presenter::collection($this->returnable);
        }

        $model_class = get_class(new $this->model());
        if ($this->returnable instanceof $model_class) {
            return new $this->presenter($this->returnable);
        }
        return $this->returnable;
    }

    /**
     * @param array $filters
     * @param array $with
     * @return AnonymousResourceCollection|null
     */
    public function first(array $filters = [], array $with = [])
    {
        $query = $this->newQuery();
        $query->with($with);
        if (!empty($filters)) {
            $this->applyFilters($filters);
            $this->injectFiltersOnQuery();
        }
        $this->order();
        $this->returnable = $query->first();
        return $this->present(true);
    }

    /**
     * @param $values
     * @param int|null $id
     * @param array $relations
     * @return AnonymousResourceCollection|null
     */
    public function storeOrUpdate($values, int $id = null, array $relations = [])
    {
        $presenter = $this->presenter;
        $this->setPresenter(null);

        if ($id) {
            $this->returnable = $this->find($id);
        } else {
            $this->returnable = new $this->model;
        }
        $this->returnable->fill($values);

        $this->persist($relations);

        if ($presenter) {
            $this->setPresenter($presenter);
        }

        return $this->present();
    }

    /**
     * @param JsonResource $resource
     */
    public function setPresenter($resource = null)
    {
        $this->presenter = $resource;
    }

    /**
     * @param int $id
     * @param array $with
     * @return AnonymousResourceCollection|null
     */
    public function find(int $id, array $with = [])
    {
        $query = $this->newQuery();
        $query->with($with);
        $this->returnable = $query->findOrFail($id);
        return $this->present();
    }

    public function persist(array $relations = [])
    {
        if ($this->isPolymorphic()) {
            return $this->persistPolymorphic($relations);
        }
        if (!empty($relations)) {
            $this->associate($relations);
        }
        return $this->returnable->save();
    }

    /**
     * @return bool
     */
    public function isPolymorphic(): bool
    {
        return $this->polymorphic;
    }

    /**
     * @param bool $polymorphic
     */
    public function setPolymorphic(bool $polymorphic): void
    {
        $this->polymorphic = $polymorphic;
    }

    public function persistPolymorphic(array $relations)
    {
        $result = false;
        foreach ($relations as $relation) {
            $model = $relation['model'];
            $polymorphic = $relation['polymorphic'];
            $result = $model->$polymorphic()->save($this->returnable);
        }
        return $result;
    }

    /**
     * @param array $relations
     */
    public function associate(array $relations)
    {
        foreach ($relations as $relation) {
            $relationship = $relation['name'];
            $this->returnable->$relationship()->associate($relation['model']);
        }
    }

    /**
     * @param int $id
     * @return int
     */
    public function destroy(int $id)
    {
        return $this->model::destroy($id);
    }

    public function setOrder($orderBy = null, $orderByType = null)
    {
        $this->orderBy = $orderBy;
        $this->orderByType = $orderByType;

        return $this;
    }
}
