<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public function scopeSearch(Builder $query, string $term)
    {
        // Adjust the fields according to your models
        $searchableFields = $this->getSearchableFields();

        $query->where(function ($query) use ($term, $searchableFields) {
            foreach ($searchableFields as $field) {
                $query->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }

    protected function getSearchableFields()
    {
        // Define the fields for search in the model using this method
        return [];
    }
}

?>
