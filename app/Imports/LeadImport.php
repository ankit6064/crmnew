<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Imports\HeadingRowImport;


class LeadImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
       
        // Create a new instance of HeadingRowImport
        $headingRow = new HeadingRowImport;

        // You can access the headings like this
        dd($headingRow->toArray());
        return new Lead([
              // Ensure this is a boolean or integer (0 or 1)
        ]);
    }
}
