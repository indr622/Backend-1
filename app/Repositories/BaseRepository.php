<?php

namespace App\Repositories;

use App\Filters\BaseFilter;
use App\Helpers\Datatable;
use App\Helpers\Pagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Yajra\DataTables\Facades\DataTables;

class BaseRepository
{
    protected $model, $view = null;

    public function baseQuery()
    {
        return is_null($this->view) ? $this->model->query() : $this->view->query();
    }

    public function getDatatables($filterClass = null)
    {
        $qry = BaseFilter::dtApply($this->baseQuery(), $filterClass);
        $baseTable = is_null($this->view) ? $this->model->getTable() : $this->view->getTable();
        $dtBuilder = BaseFilter::dtCustomSearch(DataTables::of($qry), $filterClass);

        return $dtBuilder->addIndexColumn()
            ->orderColumn('DT_RowIndex', function ($query, $order) use ($baseTable) {
                $query->orderBy($baseTable . '.id', $order);
            })
            ->make(true);
    }

    public function setBaseData($query, $request, $filter)
    {
        $request = Datatable::handle($request);
        $query = BaseFilter::apply($query, $request, $filter);
        $datas = Pagination::paginate($query, $request);
        if (array_key_exists('dt', $request) && $request['dt'] === 'true') {
            return DataTables::collection($datas['data'])
                ->filter(function () {
                })
                ->setTotalRecords($datas['meta']['total'])
                ->setFilteredRecords($datas['meta']['total'])
                ->skipPaging()
                ->make(true);
        }
        return $datas;
    }

    public function all($request, $filter)
    {
        $query = $this->model;
        $datas = $this->setBaseData($query, $request, $filter);
        return $datas;
    }

    public function allInactive($request, $filter)
    {
        $query = $this->model->onlyTrashed();
        $datas = $this->setBaseData($query, $request, $filter);
        return $datas;
    }


    /**
     * Get All Data with/without condition
     *
     * @param array
     *
     * @return Collection
     */
    public function get(array $cond = []): Collection
    {
        if (!empty($cond))
            $this->model = $this->wheremapper($this->model, $cond);

        return $this->model->get();
    }

    /**
     * Get All Data with IN condition
     *
     * @param string
     * @param array
     *
     * @return Collection
     */
    public function getWhereIn(string $field, array $cond = []): Collection
    {
        if (!empty($cond))
            $this->model = $this->model->whereIn($field, $cond);

        return $this->model->get();
    }

    /**
     * Get All Data with NOT IN condition
     *
     * @param string
     * @param array
     *
     * @return Collection
     */
    public function getWhereNotIn(string $field, array $cond = []): Collection
    {
        if (!empty($cond))
            $this->model = $this->model->whereNotIn($field, $cond);

        return $this->model->get();
    }

    /**
     * Get sorted Data
     *
     * @param string
     * @param string
     * @param array|string
     *
     * @return Collection
     */
    public function getOrderBy(String $ref, String $order = 'ASC', array $cond = []): Collection
    {
        if (!empty($cond))
            $this->model = $this->wheremapper($this->model, $cond);

        $this->model->orderBy($ref, $order);


        return $this->model->get();
    }

    /**
     * Get 1 Data by Order
     *
     * @param string
     * @param string
     * @param int
     * @param array
     *
     * @return object
     */
    public function getOrderByLimit(String $ref, String $order = 'ASC', int $limit = 1, array $cond = [])
    {
        if (!empty($cond))
            $this->model = $this->wheremapper($this->model, $cond);

        $this->model->orderBy($ref, $order);
        $this->model->limit($limit);

        return $this->model->get();
    }

    /**
     * Get a data by id.
     *
     * @param string
     *
     * @return object
     */
    public function find(String $id): object
    {
        return $this->model->find($id);
    }

    /**
     * Get a data by field.
     *
     * @param string
     * @param string
     *
     * @return object
     */
    public function findByField(String $field, String $value): object
    {
        return $this->model->where($field, $value)->first();
    }

    /**
     * Get a data by set of condition.
     *
     * @param array
     *
     * @return object
     */
    public function findWhere(array $cond): object
    {
        return $this->wheremapper($this->model, $cond)->first();
    }

    /**
     * Get a data by set of condition.
     *
     * @param string
     * @param array
     *
     * @return object
     */
    public function findWhereIn(String $field, array $values): object
    {
        return $this->model->whereIn($field, $values)->first();
    }

    /**
     * Get a data by set of condition.
     *
     * @param string
     * @param array
     *
     * @return object
     */
    public function findWhereNotIn(String $field, array $values): object
    {
        return $this->model->whereNotIn($field, $values)->first();
    }

    /**
     * Create new data.
     *
     * @param array
     *
     * @return object
     */
    public function create(array $attributes): object
    {
        return $this->model->create($attributes);
    }

    /**
     * Create batch data.
     *
     * @param array
     *
     * @return object
     */
    public function createMany(array $attributes): object
    {
        return $this->model->createMany($attributes);
    }

    /**
     * Create data from builder.
     *
     * @param Builder
     * @param array
     *
     * @return object
     */
    public function createRaw($relation, array $attributes): object
    {
        return $relation->create($attributes);
    }

    /**
     * Create batch data from builder.
     *
     * @param string
     * @param array
     *
     * @return object
     */
    public function createManyRaw($relation, array $attributes): object
    {
        return $relation->createMany($attributes);
    }

    /**
     * Update data by id.
     *
     * @param array|int
     * @param string
     *
     * @return object
     */
    public function update(array $attributes, $id)
    {
        $object = $this->model->findOrFail($id);
        $object->fill($attributes);
        $object->save();
        return $object->fresh();
    }

    /**
     * Update data by id.
     *
     * @param array|int
     * @param string
     *
     * @return object
     */
    public function updateWhere(array $attributes, array $cond): object
    {
        $object = $this->wheremapper($this->model, $cond)->first();
        $object->fill($attributes);
        $object->save();
        return $object->fresh();
    }

    /**
     * Delete data by id.
     *
     * @param string|int
     *
     * @return object
     */
    public function delete($id): object
    {
        $object = $this->model->findOrFail($id);
        return $object->delete();
    }

    /**
     * Delete data by condition.
     *
     * @param string|int
     *
     * @return object
     */
    public function deleteWhere(array $cond)
    {
        $data = $this->wheremapper($this->model, $cond);

        return $data->delete();
    }

    /**
     * Destroy data by id.
     *
     * @param string|int
     *
     * @return object
     */
    public function destroy($id)
    {
        return $this->model->withTrashed()->find($id)->destroy();
    }

    /**
     * Destroy data by id.
     *
     * @param string|int
     *
     * @return object
     */
    public function restore($id)
    {
        return $this->model->onlyTrashed()->find($id)->restore();
    }

    /**
     * Get Raw Builder
     *
     * @return builder
     */
    public function raw(): Builder
    {
        return $this->model;
    }

    /**
     * Get model with trashed data.
     *
     * @return object
     */
    public function withTrashed(): object
    {
        $this->model = $this->model->withTrashed();
        return $this;
    }

    /**
     * Get model with trashed data only.
     *
     * @return object
     */
    public function onlyTrashed(): object
    {
        $this->model = $this->model->onlyTrashed();
        return $this;
    }

    /**
     * Get model without scopes
     *
     * @param array|null
     *
     * @return builder
     */
    public function removeScopes($class = null)
    {
        if (is_null($class))
            $this->model = $this->model->withoutGlobalScopes();
        else
            $this->model = $this->model->withoutGlobalScope($class);

        return $this;
    }

    private function wheremapper($builder, $cond): Builder
    {
        if (is_array($cond)) {
            foreach ($cond as $key => $value) {
                if (is_callable($value)) {
                    $builder = $builder->where($key, $value());
                } else if (is_numeric($key)) {
                    $builder = $builder->whereRaw($value);
                } else {
                    $key = explode(' ', $key);
                    if (count($key) == 2)
                        $builder = $builder->where($key[0], $key[1], $value);
                    else
                        $builder = $builder->where($key[0], '=', $value);
                }
            }
        } else {
            $builder = $builder->whereRaw($cond);
        }

        return $builder;
    }
}
