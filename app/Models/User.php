<?php

namespace App\Models;

use App\Supports\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Searchable;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'furigana_name',
        'login_id',
        'email',
        'password',
        'unique_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getPasswordAttribute(): string
    {
        return $this->attributes['password'];
    }

    // ======================================================================
    // Accessors & Mutators
    // ======================================================================

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // ======================================================================
    // Relationships
    // ======================================================================
    public function getSearchFields(): array
    {
        return [
            'name',
            'furigana_name',
            'login_id',
            'email',
            'unique_code',
        ];
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
                'name'          => $setting,
                'furigana_name' => $setting,
                'login_id'      => $setting,
                'email'         => $setting,
                'unique_code'   => $setting,
            ],
        ];
    }
}
