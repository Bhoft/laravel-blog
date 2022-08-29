<?php

namespace App\Repositories\Eloquent;

use Exception;
use Illuminate\Support\Arr;
use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\IBase;
use App\Repositories\Criteria\ICriteria;

abstract class BaseRepository implements IBase, ICriteria
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    public function model()
    {
        return static::class;
    }

    public function all()
    {
        return $this->model->get();
    }

    public function find($id)
    {
        $result = $this->model->findOrFail($id);
        return $result;
    }

    public function findWhere($column, $value)
    {
        return $this->model->where($column, $value)->get();
    }

    public function findWhereFirst($column, $value)
    {
        return $this->model->where($column, $value)->firstOrFail();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        return $record->delete();
    }


    protected function getModelClass()
    {
        if (!method_exists($this, 'model')) {
            throw new ModelNotDefined();
        }

        // return namespace of that model
        return app()->make($this->model());
    }

    public function withCriteria(...$criteria)
    {
        // flatten criteria so its not an encasupled array
        $criteria = Arr::flatten($criteria);

        // dd($criteria);
        foreach ($criteria as $criterion) {
            // apply the criterion to this model
            $this->model = $criterion->apply($this->model);
        }

        // return $this because it should be changed
        // to the base model
        return $this;
    }
}
