<?php

namespace App\Supports\Traits;

use App\Repositories\EloquentRepository;
use Elasticsearch\Client;

/**
 * Trait Searchable
 *
 * @mixin EloquentRepository
 */
trait ElasticsearchSearchable
{
    public Client $elasticsearch;

    public string $scroll = '1m';

    /**
     * max_size =< 10000
     */
    public int $max_size = 10000;

    /**
     * @param array $conditions
     * @return array
     */
    protected function indexElasticsearch(array $conditions): array
    {
        $model = $this->model;
        $body = [];
        if (! empty($conditions)) {
            $searchKeyword = [];
            if (! empty($conditions['keyword'])) {
                $searchKeyword = [
                    'must' => [
                        'multi_match' => [
                            'query'  => $conditions['keyword'],
                            'fields' => $model->getSearchFields(),
                        ],
                    ],
                ];
            }
            $body = [
                'body' => [
                    'query' => [
                        'bool' => array_filter(array_merge([
                            'filter' => $this->getFilter($conditions),
                        ], $searchKeyword)),
                    ],
                ],
            ];
        }
        $param = array_filter(array_merge([
            'size'   => $this->max_size,
            'scroll' => $this->scroll,
            'index'  => $model->getSearchIndex(),
            'type'   => $model->getSearchType(),
        ], $body));

        return $this->elasticsearch->search($param);
    }

    /**
     * @param $conditions
     */
    public function getFilter($conditions)
    {
    }

    /**
     * @param $scrollId
     * @return array|callable
     */
    public function scrollData($scrollId)
    {
        $param = [
            'scroll'    => $this->scroll,
            'scroll_id' => $scrollId,
        ];

        return $this->elasticsearch->scroll($param);
    }
}
