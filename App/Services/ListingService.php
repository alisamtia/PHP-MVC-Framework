<?php

namespace App\Services;


class ListingService
{
    public static function categoryTree(array $all_categories): array
    {
        $categories = [];
        foreach ($all_categories as $category_p) {
            if ($category_p['parent'] == null) {
                foreach ($all_categories as $category_sub) {
                    if ($category_sub['parent'] == $category_p['id']) {
                        $categories[$category_p['name']][] = "'" . $category_sub['name'] . "'";
                    }
                }
            }
        }
        return $categories;
    }

    public static function calculateSteps($listing)
    {
        if ($listing['status'] != 'draft') {
            return 5;
        }
        if ($listing['image']) {
            return 4;
        }
        if ($listing['offerings']) {
            return 3;
        }
        if ($listing['description']) {
            return 2;
        }
        if ($listing['title']) {
            return 1;
        }

        return 0;
    }
}
