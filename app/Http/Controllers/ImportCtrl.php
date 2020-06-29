<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;

class ImportCtrl extends Controller
{
    public function importCsv(Request $request)
    {
        // Get CSV
        $aCsv = Excel::toArray(
            new ImportCtrl,
            $request->file('file')
            // , \Maatwebsite\Excel\Excel::CSV
        )[0];

        // Validate CSV
        switch ($head = $aCsv[0]) {
            // Search and Found mail
            case array_search('mail', $head, true) !== false:
                return response()->json($aCsv);
                break;

            // Not Found
            default:
                return response('Error in File. "mail" column not found or wrong delimiter. ; needed', 422);
                break;
        }

    }
}
