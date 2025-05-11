<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadImport;
use Illuminate\Support\Facades\Log;

class ImportExportService
{
    /**
     * Import users from an Excel file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool
     */
    public function importLeads($file)
    {
        try {
            // Using Laravel Excel to import data
            Excel::import(new LeadImport, $file);
            return true;
        } catch (\Exception $e) {
            // Log any errors that occur during the import process
            Log::error('Import failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Export users to an Excel file
     *
     * @return \Maatwebsite\Excel\Excel
     */
    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}
