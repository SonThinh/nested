<?php

namespace App\Models;

use App\Supports\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait, HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'parent_id',
        'name',
        '_lft',
        '_rgt',
    ];

    public function getSearchFields(): array
    {
        return [
            'parent_id',
            'name',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'model', 'model_has_files');
    }

    public function getSearchIndex(): string
    {
        return $this->getTable();
    }

    public function getSearchType(): string
    {
        return '_doc';
    }

    public function prepareRelationData(): array
    {
        return $this->toElasticsearchDocumentArray();
    }

    /**
     * @return \string[][][][][]
     */
    public function settings(): array
    {
        return [
            'analysis' => [
                'char_filter' => [
                    'normalize' => [
                        'type' => 'icu_normalizer',
                        'name' => 'nfkc',
                        'mode' => 'compose',
                    ],
                ],
                'tokenizer'   => [
                    'ja_kuromoji_tokenizer' => [
                        'mode'                   => 'search',
                        'type'                   => 'kuromoji_tokenizer',
                        'discard_compound_token' => true,
                        'user_dictionary_rules'  => [],
                    ],
                    'ja_ngram_tokenizer'    => [
                        'type'        => 'ngram',
                        'min_gram'    => 2,
                        'max_gram'    => 2,
                        'token_chars' => ['letter', 'digit'],
                    ],
                ],
                'filter'      => [
                    'ja_index_synonym'  => [
                        'type'     => 'synonym',
                        'lenient'  => false,
                        'synonyms' => [],
                    ],
                    'ja_search_synonym' => [
                        'type'     => 'synonym_graph',
                        'lenient'  => false,
                        'synonyms' => [],
                    ],
                ],
                'analyzer'    => [
                    'ja_kuromoji_index_analyzer'  => [
                        'type'        => 'custom',
                        'char_filter' => ['normalize'],
                        'tokenizer'   => 'ja_kuromoji_tokenizer',
                        'filter'      => [
                            'kuromoji_baseform',
                            'kuromoji_part_of_speech',
                            'ja_index_synonym',
                            'cjk_width',
                            'ja_stop',
                            'kuromoji_stemmer',
                            'lowercase',
                        ],
                    ],
                    'ja_kuromoji_search_analyzer' => [
                        'type'        => 'custom',
                        'char_filter' => ['normalize'],
                        'tokenizer'   => 'ja_kuromoji_tokenizer',
                        'filter'      => [
                            'kuromoji_baseform',
                            'kuromoji_part_of_speech',
                            'ja_search_synonym',
                            'cjk_width',
                            'ja_stop',
                            'kuromoji_stemmer',
                            'lowercase',
                        ],
                    ],
                    'ja_ngram_index_analyzer'     => [
                        'type'        => 'custom',
                        'char_filter' => ['normalize'],
                        'tokenizer'   => 'ja_ngram_tokenizer',
                        'filter'      => [
                            'lowercase',
                        ],
                    ],
                    'ja_ngram_search_analyzer'    => [
                        'type'        => 'custom',
                        'char_filter' => ['normalize'],
                        'tokenizer'   => 'ja_ngram_tokenizer',
                        'filter'      => [
                            'ja_search_synonym',
                            'lowercase',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \string[][][]
     */
    public function mapping(): array
    {
        $setting = [
            'type'            => 'text',
            'search_analyzer' => 'ja_kuromoji_search_analyzer',
            'analyzer'        => 'ja_kuromoji_index_analyzer',
            'fields'          => [
                'ngram' => [
                    'type'            => 'text',
                    'search_analyzer' => 'ja_ngram_search_analyzer',
                    'analyzer'        => 'ja_ngram_index_analyzer',
                ],
            ],
        ];

        return [
            'properties' => [
                'parent_id' => $setting,
                'name'      => $setting,
            ],
        ];
    }
}
