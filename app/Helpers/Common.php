<?php

if (!function_exists('pr')) {
    /**
     * Print a collection in a formatted manner.
     *
     * @param \Illuminate\Support\Collection $collection
     * @return void
     */
    function pr($collection)
    {
        // Check if the provided variable is a collection
        if ($collection instanceof \Illuminate\Support\Collection) {
            // Convert the collection to an array and format it
            $collection = $collection->toArray();
        }
        echo '<pre>';
        print_r($collection);
        echo '</pre>';
        die;
    }
}
