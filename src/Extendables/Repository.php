<?php


namespace Freelabois\LaravelQuickstart\Extendables;

use Freelabois\LaravelQuickstart\Interfaces\RepositoryInterface;
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

    /**
     * @var null|string|string[]
     */
    protected $select = null;
    protected $orderBy = null;
    protected $orderByType = null;
    protected $groupBy = null;
    private $polymorphic = false;

    /**
     * @param array $filters
     * @param array $with
     * @param int $pagination
     * @return AnonymousResourceCollection|null
     */
    public function list(array $filters = [], array $with = [], $pagination = 45)
    {
        $this->applyFilters($filters);
        $query = $this->newQuery();
        $query->with($with);
        if ($pagination === "false" || $pagination === false) {
            $pagination = 9223372036854775807;
        }
        if (!empty($this->filters)) {
            $this->applyCustomFilters();
            $this->injectFiltersOnQuery();
        }
        $this->group();
        $this->order();
        $this->returnable = $query->paginate($pagination);
        return $this->present(true);
    }

    /**
     * @param array $filters
     * @return Repository
     */
    public function applyFilters(array $filters = [])
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        $this->query = $this->model::query();
        if (!is_null($this->select)) {
            $this->query->select($this->select);
        }
        return $this->query;
    }

    /**
     * Função para customizar os filtros aplicados na consulta
     */
    protected function applyCustomFilters()
    {
    }

    /**
     *
     */
    public function injectFiltersOnQuery()
    {
        Foreach ($this->searchableFields as $searchableField) {
            $field = $searchableField['name'] ?? $searchableField['field'] ?? 'null';
            if (in_array($field, array_keys($this->filters))) {
                $value = $this->filters[$field];
                switch ($searchableField['operator'] ?? 'default') {
                    case 'in':
                        $this->query->whereIn(
                            $this->getTableName() . "." . $searchableField['field'],
                            $value
                        );
                        break;
                    case 'ilike':
                    case 'like':
                        $value = "%" . $value . "%";
                    default:
                        $this->query->where(
                            $this->getTableName() . "." . $searchableField['field'],
                            $searchableField['operator'] ?? "=",
                            $value
                        );
                        break;
                }
            }
        }
    }

    /**
     * @return $this
     */
    protected function order()
    {
        $order = $this->orderBy ?? (new $this->model)->orderBy;
        if (isset($order)) {
            $this->query->orderBy($order, $this->orderByType ?? (new $this->model)->orderByType ?? "asc");
        }
        return $this;
    }
    /**
     * @return $this
     */
    protected function group()
    {
        $group = $this->groupBy ?? (new $this->model)->groupBy;
        if (isset($group)) {
            $this->query->groupBy($group);
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
        $this->applyFilters($filters);
        $query = $this->newQuery();
        $query->with($with);
        if (!empty($this->filters)) {
            $this->applyCustomFilters();
            $this->injectFiltersOnQuery();
        }
        $this->group();
        $this->order();
        $this->returnable = $query->first();
        return $this->present(true);
    }

    /**
     * @param $values
     * @param int|null $id
     * @param array $relations
     * @return mixed|null
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
    public function find($id, array $with = [])
    {
        $ids = !is_array($id) ? [$id] : $id;
        $query = $this->newQuery();
        $query->with($with);
        $this->returnable = $query->findOrFail($ids);
        return $this->present();
    }

    /**
     * @param array $relations
     * @return bool
     */
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

    /**
     * @param array $relations
     * @return bool
     */
    public function persistPolymorphic(array $relations)
    {
        $result = false;
        Foreach ($relations as $relation) {
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
        Foreach ($relations as $relation) {
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

    /**
     * @param null $orderBy
     * @param null $orderByType
     * @return $this
     */
    public function setOrder($orderBy = null, $orderByType = null)
    {
        $this->orderBy = $orderBy;
        $this->orderByType = $orderByType;

        return $this;
    }
    /**
     * @param null $groupBy
     * @return $this
     */
    public function setGroup($groupBy = null)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @param null|string|string[] $select
     * @return $this
     */
    public function setSelect($select = null)
    {
        $this->select = $select;
        return $this;
    }


    /**
     * Return repository table name
     *
     * @return mixed
     */
    public function getTableName()
    {
        return with(new $this->model)->getTable();
    }

}
