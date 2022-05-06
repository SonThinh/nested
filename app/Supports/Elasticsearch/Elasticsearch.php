<?php

namespace App\Supports\Elasticsearch;

use App\Repositories\EloquentRepository;
use App\Supports\Traits\ElasticsearchSearchable;
use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class Elasticsearch extends EloquentRepository
{
    use ElasticsearchSearchable;

    protected Model $model;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Model $model)
    {
        parent::__construct($model);
        $this->elasticsearch = app()->make(Client::class);
        $this->model = $model;
    }

    public function index(array $conditions = [])
    {
        $conditions = request()->all();
        $items = $this->indexElasticsearch($conditions);

        return $this->getDataFromDatabase($items);
    }

    /**
     * @param $items
     * @return array
     */
    public function getIds($items): array
    {
        $_scroll_id = $items['_scroll_id'];
        $idsScroll = [];
        $itemsId = Arr::pluck($items['hits']['hits'], '_id');
        $total = Arr::get($items['hits']['total'], 'value');

        $condition = (int) floor($total / $this->max_size);

        if ($condition > 0) {

            $listIdsScroll = [];

            for ($i = 0; $i < $condition; $i++) {
                //call scroll elasticsearch
                $response = $this->scrollData($_scroll_id);
                array_push($listIdsScroll, Arr::pluck($response['hits']['hits'], '_id'));
            }

            foreach ($listIdsScroll as $value) {
                $idsScroll = array_merge($idsScroll, $value);
            }
        }

        return array_merge($itemsId, $idsScroll);
    }

    protected function getDataFromDatabase($items)
    {
        $ids = $this->getIds($items);
        //$idsOrdered = implode(',', $ids);

        //if (! request()->has('orderBy') && ! request()->has('order') && request()->has('keyword')) {
        //    $this->model->orders = [];
        //    $this->model = $this->model->whereIn('proposals.id', $ids)->orderByRaw("FIELD(proposals.id, $idsOrdered)");
        //}
        //

        return $this->model->whereIn('id', $ids);
    }
}
